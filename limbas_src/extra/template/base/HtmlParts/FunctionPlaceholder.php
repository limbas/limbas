<?php

/**
 * Class FunctionPlaceholder
 * A placeholder for a function call
 *
 * Syntax:
 *  ${=functionName} where 'functionName' is the name of a function which is called.
 * The function can have parameters of type DataPlaceholder, FunctionPlaceholder or String and should return either
 *  string, which is being inserted at the placeholder's position or
 *  TemplateElement, which is being resolved and inserted
 */
class FunctionPlaceholder extends AbstractHtmlPart {

    /**
     * @var string name of the function to call. Will be prefixed with FUNCTION_PREFIX
     */
    protected $functionName;

    /**
     * @var array of string|DataPlaceholder|FunctionPlaceholder parameters passed to function.
     *  DataPlaceholders first need to be resolved, then the function can be called
     *  FunctionPlaceholders first need to be evaluated, then the function can be called
     */
    protected $functionParams = array();

    /**
     * @var string|TemplateElement result of the function call
     */
    protected $functionResult = null;

    /**
     * @var bool whether the function has been called
     */
    protected $functionCalled;

    /**
     * If this function does not depend on report_index(), the value is false.
     *  the function can then be evaluated once, even when it is inside a table.
     * If this function depends on report_index(a), the value is a.
     * If this function depends on report_index(a) and report_index(b), the value is min(a, b)
     *  the function then has to be reevaluated every time the TableRow's index changes.
     * @var false|int
     * @see report_index()
     */
    protected $minTableIndexDependencyDepth;

    /**
     * @var null|array the last queried index
     * @see TemplateConfig::$tableRowIndex
     */
    protected $lastQueriedTableRowIndex = null;

    public function __construct($name, $params) {
        $this->functionName = $name;
        $this->functionParams = $params;
        $this->minTableIndexDependencyDepth = $this->getMinTableIndexDependencyDepth();
    }

    public function getAsHtmlArr() {
        # function call failed?
        $success = $this->tryFunctionCall(true);
        if (!$success) {
            lmb_log::error("Function {$this->functionName} could not be called!", 'Not all function placeholders could be resolved!');
            return array('');
        }

        # no result?
        if ($this->functionResult === null) {
            return array('');
        }

        # insert TemplateElement's html
        if ($this->functionResult instanceof TemplateElement) {
            return $this->functionResult->getAsHtmlArr();
        }

        # store function result
        $tc = TemplateConfig::$instance;
        if ($tc->tableRowIndex) {
            $tc->currentTableData[0][$tc->tableRowIndex[0]][$tc->tableColIndex[0]][] = $this->functionResult;
        }

        # insert function result directly
        return array($this->functionResult);
    }

    public function getUnresolvedSubTemplateElementPlaceholders() {
        # function call failed?
        $success = $this->tryFunctionCall();
        if (!$success) {
            return array();
        }

        # function returned TemplateElement -> return its placeholders
        if ($this->functionResult instanceof TemplateElement) {
            return $this->functionResult->getUnresolvedSubTemplateElementPlaceholders();
        }

        return array();
    }

    public function getUnresolvedDataPlaceholders() {
        $success = $this->tryFunctionCall();
        if ($success) {
            # function was called -> inspect result
            if ($this->functionResult instanceof TemplateElement) {
                # function returned TemplateElement -> return its placeholders
                return $this->functionResult->getUnresolvedDataPlaceholders();
            } else {
                # function called -> resolved
                return array();
            }
        } else {
            # function couldn't be called -> resolve params first
            $dataAndFunctionPlaceholders = array_filter($this->functionParams, function ($param) {
                return $param instanceof DataPlaceholder or $param instanceof FunctionPlaceholder;
            });
            return array_merge(...array_map(function (AbstractHtmlPart $placeholder) {
                return $placeholder->getUnresolvedDataPlaceholders();
            }, $dataAndFunctionPlaceholders));
        }
    }

    public function getUnresolvedTemplateGroupPlaceholders() {
        # function call failed?
        $success = $this->tryFunctionCall();
        if (!$success) {
            return array();
        }

        # function returned TemplateElement -> return its placeholders
        if ($this->functionResult instanceof TemplateElement) {
            return $this->functionResult->getUnresolvedTemplateGroupPlaceholders();
        }

        return array();
    }

    public function getUnresolvedDynamicDataPlaceholders() {
        # function call failed?
        $success = $this->tryFunctionCall();
        if (!$success) {
            return array();
        }

        # function returned TemplateElement -> return its placeholders
        if ($this->functionResult instanceof TemplateElement) {
            return $this->functionResult->getUnresolvedDynamicDataPlaceholders();
        }

        return array();
    }

    public function getAllDynamicDataPlaceholders() {
        # function call failed?
        $success = $this->tryFunctionCall();
        if (!$success) {
            return array();
        }

        # function returned TemplateElement -> return its placeholders
        if ($this->functionResult instanceof TemplateElement) {
            return $this->functionResult->getAllDynamicDataPlaceholders();
        }

        return array();
    }

    public function getTableRows() {
        # function call failed?
        $success = $this->tryFunctionCall();
        if (!$success) {
            return array();
        }

        # function returned TemplateElement -> return its template rows
        if ($this->functionResult instanceof TemplateElement) {
            return $this->functionResult->getTableRows();
        }

        return array();
    }

    /**
     * Tries to call the function. Fails if the function doesn't exist or a parameter hasn't been resolved yet
     * @param bool $isBeingRendered true if the placeholder is rendered now (last chance to be evaluated)
     * @return bool whether the function result is available
     */
    public function tryFunctionCall($isBeingRendered=false) {
        
        if (TemplateConfig::$instance->noFunctionExecute) {
            $this->functionCalled = true;
            $this->functionResult = '';
            return true;
        }
        
        
        // not dependent on table row and function already called
        if ($this->functionCalled && $this->minTableIndexDependencyDepth === false) {
            return true;
        }

        // evaluate function only when it is rendered to collect all table data
        if ($this->minTableIndexDependencyDepth === -1 && !$isBeingRendered) {
            return false;
        }

        // dependent on table row...
        if ($this->minTableIndexDependencyDepth !== false && $this->minTableIndexDependencyDepth !== -1 && !$isBeingRendered) {
            // ...but no key set -> wait for key
            if (!array_key_exists($this->minTableIndexDependencyDepth, TemplateConfig::$instance->tableRowIndex)) {
                return false;
            }
            // ...but index hasnt changed -> keep value
            if (array_slice(TemplateConfig::$instance->tableRowIndex, $this->minTableIndexDependencyDepth) == $this->lastQueriedTableRowIndex) {
                return true;
            }
        }

        # check if function exists
        $fname = TemplateConfig::$instance->getFunctionPrefix() . $this->functionName;
        if (!function_exists($fname)) {
            lmb_log::error("Function {$fname} does not exist!", 'Not all function placeholders could be resolved!');

            $this->functionCalled = true;
            $this->functionResult = '';
            return true;
        }

        # check if all params are available
        for ($i = 0; $i < count($this->functionParams); $i++) {
            $param = &$this->functionParams[$i];
            if ($param instanceof DataPlaceholder && !$param->isResolved()) {
                return false;
            } else if ($param instanceof FunctionPlaceholder && !$param->tryFunctionCall(true)) {
                return false;
            }
        }

        $params = array_map(function ($param) {
            if ($param instanceof DataPlaceholder) {
                return $param->getValue();
            } else if ($param instanceof FunctionPlaceholder) {
                return $param->functionResult;
            } else {
                return $param;
            }
        }, $this->functionParams);

        $result = call_user_func_array($fname, $params);
        $this->functionCalled = true;
        $this->functionResult = $result;

        // store for which value the index was calculated
        if ($this->minTableIndexDependencyDepth !== false) {
            $this->lastQueriedTableRowIndex = array_slice(TemplateConfig::$instance->tableRowIndex, $this->minTableIndexDependencyDepth);
        }

        return true;
    }

    public function result() {
        if ($this->tryFunctionCall(true)) {
            return $this->functionResult;
        }
        return null;
    }

    /**
     * @return bool whether the function result is truthy
     * @see IfPlaceholder
     */
    public function resultTruthy() {
        return $this->functionResult ? true : false;
    }

    public function getFunctionName() {
        return $this->functionName;
    }

    public function getTemplateElement() {
        if ($this->tryFunctionCall() && $this->functionResult instanceof TemplateElement) {
            return $this->functionResult;
        }
        return null;
    }

    /**
     * @return false|int
     * @see FunctionPlaceholder::$minTableIndexDependencyDepth
     */
    public function getMinTableIndexDependencyDepth() {
        if ($this->functionName === 'index') {
            if (count($this->functionParams) < 2) {
                return 0;
            } else if ($this->functionParams[1] instanceof DataPlaceholder) {
                return false;
            } else if ($this->functionParams[1] instanceof FunctionPlaceholder) {
                return false;
            } else {
                return $this->functionParams[1] ? intval($this->functionParams[1]) : 0;
            }
        } else if ($this->functionName === 'tableRowData') {
            return -1;
        } else if ($this->functionName === 'datid') {
            if (count($this->functionParams) < 1) {
                return 0;
            } else if ($this->functionParams[0] instanceof DataPlaceholder) {
                return false;
            } else if ($this->functionParams[0] instanceof FunctionPlaceholder) {
                return false;
            } else {
                return $this->functionParams[0] ? intval($this->functionParams[0]) : 0;
            }
        }
        $min = false;
        foreach ($this->functionParams as &$param) {
            if ($param instanceof FunctionPlaceholder) {
                $newMin = $param->getMinTableIndexDependencyDepth();
                if ($min === false) {
                    $min = $newMin;
                } else if ($newMin !== false) {
                    $min = min($min, $newMin);
                }
            }
        }
        return $min;
    }
}

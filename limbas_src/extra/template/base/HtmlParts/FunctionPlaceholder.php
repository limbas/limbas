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
    protected $functionResult;

    /**
     * @var bool whether the function has been called
     */
    protected $functionCalled;

    public function __construct($name, $params) {
        $this->functionName = $name;
        $this->functionParams = $params;
    }

    public function getAsHtmlArr() {
        # function call failed?
        $success = $this->tryFunctionCall();
        if (!$success) {
            lmb_log::error("Function {$this->functionName} could not be called!", 'Not all data placeholders could be resolved!');

            return array('');
        }

        # no result?
        if (!$this->functionResult) {
            return array('');
        }

        # insert TemplateElement's html
        if ($this->functionResult instanceof TemplateElement) {
            return $this->functionResult->getAsHtmlArr();
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

    /**
     * Tries to call the function. Fails if the function doesn't exist or a parameter hasn't been resolved yet
     * @return bool whether the function result is available
     */
    public function tryFunctionCall() {
        if ($this->functionCalled) {
            return true;
        }

        # check if function exists
        $fname = TemplateConfig::$instance->getFunctionPrefix() . $this->functionName;
        if (!function_exists($fname)) {
            lmb_log::error("Function {$fname} does not exist!", 'Not all data placeholders could be resolved!');

            $this->functionCalled = true;
            $this->functionResult = '';
            return true;
        }

        # check if all params are available
        for ($i = 0; $i < count($this->functionParams); $i++) {
            $param = &$this->functionParams[$i];
            if ($param instanceof DataPlaceholder && !$param->isResolved()) {
                return false;
            } else if ($param instanceof FunctionPlaceholder && !$param->tryFunctionCall()) {
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
        return true;
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

}
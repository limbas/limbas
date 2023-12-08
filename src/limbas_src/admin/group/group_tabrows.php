<?php

global $farbschema;
global $f_result;
global $l_result;
global $s_result;
global $is_popup;
global $ID;
global $gsr;
global $searchlang;
global $lang;
global $group_level;
global $session;
global $lmcurrency;
global $ext_fk;
global $gtrigger;
global $gtab;
global $gfield;

if ($gtab["typ"][$gtabid] == 5) {
    $isview = 1;
}
?>

    <div ID="table_<?= $gtabid ?>" class="container-fluid w-100 px-0 pt-1">
        <table class="table table-sm table-striped">
            <thead class="sticky-top bg-white mt-1">
            <tr>
                <td colspan="4" class="align-top">
                    <table>
                        <tr>
                            <?php
                            # --- view ---
                            ?>
                            <td nowrap class="p-0 pe-2 align-top">
                                <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                    <label class="form-check-label" for="checkbox_<?= "tabhidemenu" ?>_<?= $gtabid ?>">
                                        <i class="<?= "lmb-icon lmb-eye-slash" ?>"
                                           title="<?= $lang[2302] ?>"></i>
                                    </label>
                                    <div>
                                        <input class="form-check-input m-0 mt-1"
                                               id="checkbox_<?= "tabhidemenu" ?>_<?= $gtabid ?>"
                                               type="checkbox"
                                               name="<?= "tabhidemenu" ?>_<?= $gtabid ?>"
                                               onclick="save_rules('<?= $gtabid ?>','',<?= 18 ?>)" <?= $f_result[$gtabid]["hidemenu"] == 1 ? "checked" : "" ?>>
                                    </div>
                                </div>
                            </td>
                            <?php

                            if (!$isview) {
                                # --- view versions ---
                                if ($gtab["versioning"][$gtabid]) {
                                    if ((($l_result[$gtabid]["viewver"] == 1 or !$l_result) and $s_result[$gtabid]["viewver"]) or $session["superadmin"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="checkbox_<?= "tabviewver" ?>_<?= $gtabid ?>">
                                                    <i class="<?= "lmb-icon-cus lmb-copy-eye" ?>"
                                                       title="<?= $lang[2356] ?>"></i>
                                                </label>
                                                <input class="form-check-input m-0 mt-1"
                                                       id="checkbox_<?= "tabviewver" ?>_<?= $gtabid ?>"
                                                       type="checkbox"
                                                       name="<?= "tabviewver" ?>_<?= $gtabid ?>"
                                                       onclick="save_rules('<?= $gtabid ?>','',<?= 20 ?>)" <?= $f_result[$gtabid]["viewver"] == 1 ? "checked" : "" ?>>
                                            </div>
                                        </td>
                                        <?php
                                    } elseif (!$l_result[$gtabid]["viewver"] and $s_result[$gtabid]["viewver"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="disabledcheckbox_<?= $lang[2356] ?>">
                                                    <i class="<?= "lmb-icon-cus lmb-copy-eye" ?> opacity-50"
                                                       title="<?= $lang[2356] ?>"></i>
                                                </label>
                                                <input
                                                        class="form-check-input m-0 mt-1"
                                                        id="disabledcheckbox_<?= $lang[2356] ?>"
                                                        type="checkbox"
                                                        readonly
                                                        disabled>
                                            </div>
                                        </td>
                                        <?php
                                    }

                                    if ((($l_result[$gtabid]["editver"] == 1 or !$l_result) and $s_result[$gtabid]["editver"]) or $session["superadmin"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="checkbox_<?= "tabeditver" ?>_<?= $gtabid ?>">
                                                    <i class="<?= "lmb-icon-cus lmb-copy-edit" ?>"
                                                       title="<?= $lang[3010] ?>"></i>
                                                </label>
                                                <input class="form-check-input m-0 mt-1"
                                                       id="checkbox_<?= "tabeditver" ?>_<?= $gtabid ?>"
                                                       type="checkbox"
                                                       name="<?= "tabeditver" ?>_<?= $gtabid ?>"
                                                       onclick="save_rules('<?= $gtabid ?>','',<?= 201 ?>)" <?= $f_result[$gtabid]["editver"] == 1 ? "checked" : "" ?>>
                                            </div>
                                        </td>
                                        <?php
                                    } elseif (!$l_result[$gtabid]["editver"] and $s_result[$gtabid]["editver"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="disabledcheckbox_<?= $lang[3010] ?>">
                                                    <i class="<?= "lmb-icon-cus lmb-copy-edit" ?> opacity-50"
                                                       title="<?= $lang[3010] ?>"></i>
                                                </label>
                                                <input
                                                        class="form-check-input m-0 mt-1"
                                                        id="disabledcheckbox_<?= $lang[3010] ?>"
                                                        type="checkbox"
                                                        readonly
                                                        disabled>
                                            </div>
                                        </td>
                                        <?php
                                    }

                                    if ((($l_result[$gtabid]["delver"] == 1 or !$l_result) and $s_result[$gtabid]["delver"]) or $session["superadmin"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="checkbox_<?= "tabdelver" ?>_<?= $gtabid ?>">
                                                    <i class="<?= "lmb-icon-cus lmb-copy-minus" ?>"
                                                       title="<?= $lang[3009] ?>"></i>
                                                </label>
                                                <input class="form-check-input m-0 mt-1"
                                                       id="checkbox_<?= "tabdelver" ?>_<?= $gtabid ?>"
                                                       type="checkbox"
                                                       name="<?= "tabdelver" ?>_<?= $gtabid ?>"
                                                       onclick="save_rules('<?= $gtabid ?>','',<?= 202 ?>)" <?= $f_result[$gtabid]["delver"] == 1 ? "checked" : "" ?>>
                                            </div>
                                        </td>
                                        <?php
                                    } elseif (!$l_result[$gtabid]["delver"] and $s_result[$gtabid]["delver"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="disabledcheckbox_<?= $lang[3009] ?>">
                                                    <i class="<?= "lmb-icon-cus lmb-copy-minus" ?> opacity-50"
                                                       title="<?= $lang[3009] ?>"></i>
                                                </label>
                                                <input
                                                        class="form-check-input m-0 mt-1"
                                                        id="disabledcheckbox_<?= $lang[3009] ?>"
                                                        type="checkbox"
                                                        readonly
                                                        disabled>
                                            </div>
                                        </td>
                                        <?php
                                    }

                                }
                                # --- unlock data ---
                                if ($gtab["lockable"][$gtabid]) {
                                    if ((($l_result[$gtabid]["lock"] == 1 or !$l_result) and $s_result[$gtabid]["lock"]) or $session["superadmin"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="checkbox_<?= "lock" ?>_<?= $gtabid ?>">
                                                    <i class="<?= "lmb-icon lmb-lock" ?>"
                                                       title="<?= $lang[2428] ?>"></i>
                                                </label>
                                                <input class="form-check-input m-0 mt-1"
                                                       id="checkbox_<?= "lock" ?>_<?= $gtabid ?>"
                                                       type="checkbox"
                                                       name="<?= "lock" ?>_<?= $gtabid ?>"
                                                       onclick="save_rules('<?= $gtabid ?>','',<?= 21 ?>)" <?= $f_result[$gtabid]["lock"] == 1 ? "checked" : "" ?>>
                                            </div>
                                        </td>
                                        <?php
                                    } elseif (!$l_result[$gtabid]["lock"] and $s_result[$gtabid]["lock"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="disabledcheckbox_<?= $lang[2428] ?>">
                                                    <i class="<?= "lmb-icon lmb-lock" ?> opacity-50"
                                                       title="<?= $lang[2428] ?>"></i>
                                                </label>
                                                <input
                                                        class="form-check-input m-0 mt-1"
                                                        id="disabledcheckbox_<?= $lang[2428] ?>"
                                                        type="checkbox"
                                                        readonly
                                                        disabled>
                                            </div>
                                        </td>
                                        <?php
                                    }
                                }

                                # Dataset-Rules
                                if ($gtab["has_userrules"][$gtabid]) {
                                    # --- set userrules for administrate datasets ---
                                    if ((($l_result[$gtabid]["userrules"] == 1 or !$l_result) and $s_result[$gtabid]["edit_userrules"]) or $session["superadmin"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="checkbox_<?= "tabuserrules" ?>_<?= $gtabid ?>">
                                                    <i class="<?= "lmb-icon lmb-group-gear" ?>"
                                                       title="<?= $lang[2337] ?>"></i>
                                                </label>
                                                <input class="form-check-input m-0 mt-1"
                                                       id="checkbox_<?= "tabuserrules" ?>_<?= $gtabid ?>"
                                                       type="checkbox"
                                                       name="<?= "tabuserrules" ?>_<?= $gtabid ?>"
                                                       onclick="save_rules('<?= $gtabid ?>','',<?= 19 ?>)" <?= $f_result[$gtabid]["userrules"] == 1 ? "checked" : "" ?>>
                                            </div>
                                        </td>
                                        <?php
                                    } elseif (!$l_result[$gtabid]["userrules"] and $s_result[$gtabid]["edit_userrules"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="disabledcheckbox_<?= $lang[2337] ?>">
                                                    <i class="<?= "lmb-icon lmb-group-gear" ?> opacity-50"
                                                       title="<?= $lang[2337] ?>"></i>
                                                </label>
                                                <input
                                                        class="form-check-input m-0 mt-1"
                                                        id="disabledcheckbox_<?= $lang[2337] ?>"
                                                        type="checkbox"
                                                        readonly
                                                        disabled>
                                            </div>
                                        </td>
                                        <?php
                                    }
                                    # --- set userrules for manage created datasets ---
                                    if ((($l_result[$gtabid]["userprivilege"] == 1 or !$l_result) and $s_result[$gtabid]["edit_ownuserrules"]) or $session["superadmin"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="checkbox_<?= "tabuserprivilege" ?>_<?= $gtabid ?>">
                                                    <i class="<?= "lmb-icon lmb-user-gear" ?>"
                                                       title="<?= $lang[2453] ?>"></i>
                                                </label>
                                                <input class="form-check-input m-0 mt-1"
                                                       id="checkbox_<?= "tabuserprivilege" ?>_<?= $gtabid ?>"
                                                       type="checkbox"
                                                       name="<?= "tabuserprivilege" ?>_<?= $gtabid ?>"
                                                       onclick="save_rules('<?= $gtabid ?>','',<?= 28 ?>)" <?= $f_result[$gtabid]["userprivilege"] == 1 ? "checked" : "" ?>>
                                            </div>
                                        </td>
                                        <?php
                                    } elseif (!$l_result[$gtabid]["userprivilege"] and $s_result[$gtabid]["edit_ownuserrules"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="disabledcheckbox_<?= $lang[2453] ?>">
                                                    <i class="<?= "lmb-icon lmb-user-gear" ?> opacity-50"
                                                       title="<?= $lang[2453] ?>"></i>
                                                </label>
                                                <input
                                                        class="form-check-input m-0 mt-1"
                                                        id="disabledcheckbox_<?= $lang[2453] ?>"
                                                        type="checkbox"
                                                        readonly
                                                        disabled>
                                            </div>
                                        </td>
                                        <?php
                                    }
                                    # --- set userrules for manage single user/groups ---
                                    if ((($l_result[$gtabid]["hierarchicprivilege"] == 1 or !$l_result) and $s_result[$gtabid]["hierarchicprivilege"]) or $session["superadmin"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="dropdown">
                                                <i class="lmb-icon lmb-groups cursor-pointer"
                                                   data-bs-toggle="dropdown"
                                                   TITLE="<?= $lang[2516] ?>"
                                                   onclick="save_rules('$gtabid','',30)"></i>
                                                <input TYPE="checkbox" NAME="tabhierarchicprivilege_<?= $gtabid ?>"
                                                       onclick="save_rules('$gtabid','',29)"
                                                    <?= $f_result[$gtabid]["hierarchicprivilege"] == 1 ? "checked" : "" ?>>
                                                <?php
                                                $glitems["name"] = array("view_$gtabid", "edit_$gtabid", "delete_$gtabid");
                                                $glitems["header"] = array("<i class=\"lmb-icon lmb-eye\"></i>", "<i class=\"lmb-icon lmb-pencil\"></i>", "<i class=\"lmb-icon lmb-trash\"></i>");
                                                ?>
                                                <div class="dropdown-menu">
                                                    <?php
                                                    getGroupTree("GroupSelect_" . $gtabid, $glitems, $f_result[$gtabid]["specificprivilege"]);
                                                    ?>
                                                </div>
                                            </div>
                                        </td>
                                        <?php
                                    } elseif (!$l_result[$gtabid]["hierarchicprivilege"] and $s_result[$gtabid]["hierarchicprivilege"]) {
                                        ?>
                                        <td nowrap class="p-0 pe-2 align-top">
                                            <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                                <label class="form-check-label"
                                                       for="disabledcheckbox_<?= $lang[2516] ?>">
                                                    <i class="<?= "lmb-icon lmb-groups" ?> opacity-50"
                                                       title="<?= $lang[2516] ?>"></i>
                                                </label>
                                                <input
                                                        class="form-check-input m-0 mt-1"
                                                        id="disabledcheckbox_<?= $lang[2516] ?>"
                                                        type="checkbox"
                                                        readonly
                                                        disabled>
                                            </div>
                                        </td>
                                        <?php
                                    }
                                }
                                # --- add ---

                                if ((($l_result[$gtabid]["add"] == 1 or !$l_result) and $s_result[$gtabid]["add"]) or $session["superadmin"]) {
                                    ?>
                                    <td nowrap class="p-0 pe-2 align-top">
                                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                            <label class="form-check-label"
                                                   for="checkbox_<?= "tabadd" ?>_<?= $gtabid ?>">
                                                <i class="<?= "lmb-icon-cus lmb-page-new" ?>"
                                                   title="<?= $lang[571] ?>"></i>
                                            </label>
                                            <input class="form-check-input m-0 mt-1"
                                                   id="checkbox_<?= "tabadd" ?>_<?= $gtabid ?>"
                                                   type="checkbox"
                                                   name="<?= "tabadd" ?>_<?= $gtabid ?>"
                                                   onclick="save_rules('<?= $gtabid ?>','',<?= 6 ?>)" <?= $f_result[$gtabid]["add"] == 1 ? "checked" : "" ?>>
                                        </div>
                                    </td>
                                    <?php
                                } elseif (!$l_result[$gtabid]["add"] and $s_result[$gtabid]["add"]) {
                                    ?>
                                    <td nowrap class="p-0 pe-2 align-top">
                                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                            <label class="form-check-label" for="disabledcheckbox_<?= $lang[571] ?>">
                                                <i class="<?= "lmb-icon-cus lmb-page-new" ?> opacity-50"
                                                   title="<?= $lang[571] ?>"></i>
                                            </label>
                                            <input
                                                    class="form-check-input m-0 mt-1"
                                                    id="disabledcheckbox_<?= $lang[571] ?>"
                                                    type="checkbox"
                                                    readonly
                                                    disabled>
                                        </div>
                                    </td>
                                    <?php
                                }

                                # --- delete ---

                                if ((($l_result[$gtabid]["delete"] == 1 or !$l_result) and $s_result[$gtabid]["delete"]) or $session["superadmin"]) {
                                    ?>
                                    <td nowrap class="p-0 pe-2 align-top">
                                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                            <label class="form-check-label"
                                                   for="checkbox_<?= "tabdel" ?>_<?= $gtabid ?>">
                                                <i class="<?= "lmb-icon lmb-page-delete-alt" ?>"
                                                   title="<?= $lang[160] ?>"></i>
                                            </label>
                                            <input class="form-check-input m-0 mt-1"
                                                   id="checkbox_<?= "tabdel" ?>_<?= $gtabid ?>"
                                                   type="checkbox"
                                                   name="<?= "tabdel" ?>_<?= $gtabid ?>"
                                                   onclick="save_rules('<?= $gtabid ?>','',<?= 4 ?>)" <?= $f_result[$gtabid]["delete"] == 1 ? "checked" : "" ?>>
                                        </div>
                                    </td>
                                    <?php
                                } elseif (!$l_result[$gtabid]["delete"] and $s_result[$gtabid]["delete"]) {
                                    ?>
                                    <td nowrap class="p-0 pe-2 align-top">
                                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                            <label class="form-check-label" for="disabledcheckbox_<?= $lang[160] ?>">
                                                <i class="<?= "lmb-icon lmb-page-delete-alt" ?> opacity-50"
                                                   title="<?= $lang[160] ?>"></i>
                                            </label>
                                            <input
                                                    class="form-check-input m-0 mt-1"
                                                    id="disabledcheckbox_<?= $lang[160] ?>"
                                                    type="checkbox"
                                                    readonly
                                                    disabled>
                                        </div>
                                    </td>
                                    <?php
                                }

                                # --- trash ---

                                if ((($l_result[$gtabid]["trash"] == 1 or !$l_result) and $s_result[$gtabid]["trash"]) or $session["superadmin"]) {
                                    ?>
                                    <td nowrap class="p-0 pe-2 align-top">
                                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                            <label class="form-check-label"
                                                   for="checkbox_<?= "tabtrash" ?>_<?= $gtabid ?>">
                                                <i class="<?= "lmb-icon lmb-trash" ?>"
                                                   title="<?= $lang[160] ?>"></i>
                                            </label>
                                            <input class="form-check-input m-0 mt-1"
                                                   id="checkbox_<?= "tabtrash" ?>_<?= $gtabid ?>"
                                                   type="checkbox"
                                                   name="<?= "tabtrash" ?>_<?= $gtabid ?>"
                                                   onclick="save_rules('<?= $gtabid ?>','',<?= 7 ?>)" <?= $f_result[$gtabid]["trash"] == 1 ? "checked" : "" ?>>
                                        </div>
                                    </td>
                                    <?php
                                } elseif (!$l_result[$gtabid]["trash"] and $s_result[$gtabid]["trash"]) {
                                    ?>
                                    <td nowrap class="p-0 pe-2 align-top">
                                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                            <label class="form-check-label" for="disabledcheckbox_<?= $lang[160] ?>">
                                                <i class="<?= "lmb-icon lmb-trash" ?> opacity-50"
                                                   title="<?= $lang[160] ?>"></i>
                                            </label>
                                            <input
                                                    class="form-check-input m-0 mt-1"
                                                    id="disabledcheckbox_<?= $lang[160] ?>"
                                                    type="checkbox"
                                                    readonly
                                                    disabled>
                                        </div>
                                    </td>
                                    <?php
                                }

                                # --- archive ---

                                if ((($l_result[$gtabid]["hide"] == 1 or !$l_result) and $s_result[$gtabid]["hide"]) or $session["superadmin"]) {
                                    ?>
                                    <td nowrap class="p-0 pe-2 align-top">
                                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                            <label class="form-check-label"
                                                   for="checkbox_<?= "tabhide" ?>_<?= $gtabid ?>">
                                                <i class="<?= "lmb-icon lmb-page-key" ?>"
                                                   title="<?= $lang[1257] ?>"></i>
                                            </label>
                                            <input class="form-check-input m-0 mt-1"
                                                   id="checkbox_<?= "tabhide" ?>_<?= $gtabid ?>"
                                                   type="checkbox"
                                                   name="<?= "tabhide" ?>_<?= $gtabid ?>"
                                                   onclick="save_rules('<?= $gtabid ?>','',<?= 5 ?>)" <?= $f_result[$gtabid]["hide"] == 1 ? "checked" : "" ?>>
                                        </div>
                                    </td>
                                    <?php
                                } elseif (!$l_result[$gtabid]["hide"] and $s_result[$gtabid]["hide"]) {
                                    ?>
                                    <td nowrap class="p-0 pe-2 align-top">
                                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                            <label class="form-check-label" for="disabledcheckbox_<?= $lang[1257] ?>">
                                                <i class="<?= "lmb-icon lmb-page-key" ?> opacity-50"
                                                   title="<?= $lang[1257] ?>"></i>
                                            </label>
                                            <input
                                                    class="form-check-input m-0 mt-1"
                                                    id="disabledcheckbox_<?= $lang[1257] ?>"
                                                    type="checkbox"
                                                    readonly
                                                    disabled>
                                        </div>
                                    </td>
                                    <?php
                                }

                            }
                            ?>
                        </tr>
                    </table>

                    <?php
                    # --- view ---
                    ?>

                </td>
                <td>
                    <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                        <label class="form-check-label" for="checkbox_<?= "tabview" ?>_<?= $gtabid ?>">
                            <i
                                    class="<?= "lmb-icon lmb-eye" ?>"
                                    title="<?= $lang[2303] ?>">
                            </i>
                        </label>
                        <input
                                class="form-check-input m-0 mt-1"
                                type="checkbox"
                                id="checkbox_<?= "tabview" ?>_<?= $gtabid ?>"
                                name="<?= "tabview" ?>_<?= $gtabid ?>"
                                data-ruletype="1"
                                data-gtabid="<?= $gtabid ?>"
                                <?= $isview ? 'data-isview="true"' : '' ?>
                            <?= $f_result[$gtabid]["tabview"] ? "checked" : "" ?>>
                    </div>
                </td>
                <?php
                ?>


                <?php
                if (!$isview) {

                    # --- edit ---
                    ?>
                    <td class="align-top">
                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                            <label class="form-check-label" for="checkbox_<?= "tabedit" ?>_<?= $gtabid ?>">
                                <i
                                        class="<?= "lmb-icon lmb-pencil" ?>"
                                        title="<?= $lang[1259] ?>">
                                </i>
                            </label>
                            <input
                                    class="form-check-input m-0 mt-1"
                                    type="checkbox"
                                    id="checkbox_<?= "tabedit" ?>_<?= $gtabid ?>"
                                    name="<?= "tabedit" ?>_<?= $gtabid ?>"
                                    data-ruletype="2"
                                    data-gtabid="<?= $gtabid ?>"
                                <?= $f_result[$gtabid]["tabedit"] ? "checked" : "" ?>>
                        </div>
                    </td>
                    <?php

                    # --- need ---
                    ?>
                    <td class="align-top">
                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                            <label class="form-check-label" for="checkbox_<?= "tabneed" ?>_<?= $gtabid ?>">
                                <i
                                        class="<?= "lmb-icon lmb-exclamation" ?>"
                                        title="<?= $lang[1508] ?>">
                                </i>
                            </label>
                            <input
                                    class="form-check-input m-0 mt-1"
                                    type="checkbox"
                                    id="checkbox_<?= "tabneed" ?>_<?= $gtabid ?>"
                                    name="<?= "tabneed" ?>_<?= $gtabid ?>"
                                    data-ruletype="9"
                                    data-gtabid="<?= $gtabid ?>"
                                <?= $f_result[$gtabid]["tabneed"] ? "checked" : "" ?>>
                        </div>
                    </td>
                    <?php

                    # --- copy ---
                    ?>
                    <td class="align-top">
                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                            <label class="form-check-label" for="checkbox_<?= "tabcopy" ?>_<?= $gtabid ?>">
                                <i
                                        class="<?= "lmb-icon lmb-copy" ?>"
                                        title="<?= $lang[1464] ?>">
                                </i>
                            </label>
                            <input
                                    class="form-check-input m-0 mt-1"
                                    type="checkbox"
                                    id="checkbox_<?= "tabcopy" ?>_<?= $gtabid ?>"
                                    name="<?= "tabcopy" ?>_<?= $gtabid ?>"
                                    data-ruletype="13"
                                    data-gtabid="<?= $gtabid ?>"
                                <?= $f_result[$gtabid]["tabcopy"] ? "checked" : "" ?>>
                        </div>
                    </td>
                    <?php

                    # --- list edit ---
                    ?>
                    <td class="align-top">
                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                <i class="lmb-icon-cus lmb-list-edit" TITLE="<?= $lang[1290] ?>"></i>
                        </div>
                    </td>
                    <?php
                }

                # --- options ---
                ?>
                <td class="align-top">
                    <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                        <label class="form-check-label" for="checkbox_<?= "taboptions" ?>_<?= $gtabid ?>">
                            <i
                                    class="<?= "lmb-icon lmb-cog-alt" ?>"
                                    title="<?= $lang[2795] ?>">
                            </i>
                        </label>
                        <input
                                class="form-check-input m-0 mt-1"
                                type="checkbox"
                                id="checkbox_<?= "taboptions" ?>_<?= $gtabid ?>"
                                name="<?= "taboptions" ?>_<?= $gtabid ?>"
                                data-ruletype="32"
                                data-gtabid="<?= $gtabid ?>"
                            <?= $f_result[$gtabid]["taboption"] ? "checked" : "" ?>>
                    </div>
                </td>
                <?php

                # --- speech recognition ---
                if (!$isview) {
                    ?>
                    <td class="align-top">
                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                <i class="lmb-icon lmb-microphone" TITLE="Speech recognition"></i>
                        </div>
                    </td>
                    <?php
                }

                # --- versioning ---
                if (!$isview and $gtab["versioning"][$gtabid] and $f_result[$gtabid]["versioning_type"] == 2) {
                    ?>
                    <td class="align-top">
                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                            <label class="form-check-label" for="checkbox_<?= "versioning" ?>_<?= $gtabid ?>">
                                <i
                                        class="<?= "lmb-icon lmb-versioning-type" ?>"
                                        title="<?= $lang[2132] ?>">
                                </i>
                            </label>
                            <input
                                    class="form-check-input m-0 mt-1"
                                    type="checkbox"
                                    id="checkbox_<?= "versioning" ?>_<?= $gtabid ?>"
                                    name="<?= "versioning" ?>_<?= $gtabid ?>"
                                    data-ruletype="16"
                                    data-gtabid="<?= $gtabid ?>"
                                <?= @in_array("1", $f_result[$gtabid]["versionable"]) ? "checked" : "" ?>>
                        </div>
                    </td>
                    <?php
                }

                # ---- IS VIEW ------
                if ($isview) {
                    ?>
                    <td class="align-top">
                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                            <i class="lmb-icon lmb-colors" TITLE="<?= $lang[2567] ?>"></i>
                        </div>
                    </td>
                    <td colspan="4"></td>
                    <?php
                    if (!$f_result[$gtabid]["field_id"]) {
                        ?>
                        <td style="width:700px;color:red">
                            <b><?= $lang[2699] ?></b>
                        </td>
                        <?php
                    }
                }

                if (!$isview) {
                    ?>
                    <td class="align-top">
                        <div class="form-check d-flex flex-column p-0 align-items-center justify-content-between m-0">
                                <i class="lmb-icon lmb-colors" TITLE="<?= $lang[2567] ?>"></i>
                        </div>
                    </td>
                    <td TITLE="<?= $lang[2568] ?>">
                        <b><?= $lang[1614] ?></b>
                    </td>
                    <?php #todo add title to $lang ?>
                    <td data-filterruleid="<?= $gtabid ?>" style="cursor:pointer" title="<?="Default filter rules can be set by first using the filter in the table view and clicking here"?>">
                        <i class="fa-solid fa-filter"></i>
                        <b>
                            <U><?= $lang[2569] ?></U>
                        </b>
                    </td>
                    <td TITLE="<?= $lang[2570] ?>">
                        <b><?= $lang[2570] ?></b>
                    </td>
                    <?php
                    if ($gtrigger[$gtabid]) {
                        ?>
                        <td>
                            <b><?= $lang[1987] ?></b>
                        </td>
                        <?php
                    }
                    ?>
                    <td TITLE="<?= $lang[2572] ?>">
                        <b>&nbsp;<?= $lang[1563] ?></b>
                    </td>
                    <td>
                        <b>&nbsp;<?= $lang[1986] ?></b>
                    </td>
                    <?php
                }
                ?>
            </tr>
            </thead>


            <?php
            if ($f_result[$gtabid]["field_id"]) {
                foreach ($f_result[$gtabid]["field_id"] as $value => $key) {
                    ?>
                    <tr class="">

                        <td style="height:20px;">
                            <?= $key ?>
                        </td>
                        <td TITLE="<?= $f_result[$gtabid]["beschreibung"][$key] ?>" nowrap>
                            <?= $f_result[$gtabid]['field'][$key] ?>&nbsp;(<?= $key ?>)
                        </td>
                        <td COLSPAN="2" nowrap>
                            <?= $f_result[$gtabid]["typ"][$key] ?>
                        </td>
                        <?php
                        # --- view ----
                        ?>
                        <td>
                            <?php
                            if ($l_result[$gtabid]["view"][$key] or !$l_result) {
                                ?>
                                <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                    <input class="form-check-input m-0"
                                           type="checkbox"
                                           name="<?= "viewrule" ?>_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                           onclick="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]["field_id"][$key] ?>',<?= 1 ?>)" <?= $f_result[$gtabid]["view"][$key] ? "checked" : "" ?>>
                                </div>
                                <?php
                            } else {
                                ?>
                                <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                    <input
                                            class="form-check-input opacity-50"
                                            type="checkbox"
                                            readonly
                                            disabled>
                                </div>
                                <?php
                                echo '</td><td colspan="12"></td></tr>';
                                continue;
                            }
                            ?>
                        </td>
                        <?php

                        # --- edit ----

                        if (($s_result[$gtabid]["edit"][$key] or $session["superadmin"]) and $f_result[$gtabid]["field_type"][$key] < 100) {
                            if (!$isview) {
                                ?>
                                <td>
                                    <?php
                                    if ($l_result[$gtabid]["edit"][$key] or !$l_result) {
                                        ?>
                                        <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                            <input class="form-check-input m-0"
                                                   type="checkbox"
                                                   name="<?= "edit" ?>_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                                   onclick="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]["field_id"][$key] ?>',<?= 2 ?>)" <?= $f_result[$gtabid]["edit"][$key] ? "checked" : "" ?>>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                            <input
                                                    class="form-check-input opacity-50"
                                                    type="checkbox"
                                                    readonly
                                                    disabled>
                                        </div>
                                        <?php
                                    }
                                    ?>
                                </td>
                                <?php
                            }
                        } else {
                            echo '</td><td colspan="12"></td></tr>';
                            continue;
                        }


                        # --- need ----
                        if (!$isview) {
                            ?>
                            <td>
                                <?php
                                if ($f_result[$gtabid]["field_type"][$key] < 100 and $f_result[$gtabid]["data_type"][$key] != 22 and $f_result[$gtabid]["field_type"][$key] != 14 and $f_result[$gtabid]["field_type"][$key] != 15 and $f_result[$gtabid]["field_type"][$key] != 16 and $f_result[$gtabid]["field_type"][$key] != 19 and $f_result[$gtabid]["field_type"][$key] != 6 and $f_result[$gtabid]["field_type"][$key] != 9 and $f_result[$gtabid]["field_type"][$key] != 17 and $f_result[$gtabid]["field_type"][$key] != 20 and !$f_result[$gtabid]["argument_typ"][$key]) {
                                    if (!$l_result[$gtabid]["need"][$key]) {
                                        ?>
                                        <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                            <input class="form-check-input m-0"
                                                   type="checkbox"
                                                   name="<?= "needrule" ?>_<?= $gtabid ?>_<?= $f_result[$gtabid]['field_id'][$key] ?>"
                                                   onclick="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]['field_id'][$key] ?>',<?= 9 ?>)" <?= $f_result[$gtabid]["need"][$key] == 1 ? "checked" : "" ?>>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                            <input
                                                    class="form-check-input opacity-50"
                                                    type="checkbox"
                                                    readonly
                                                    disabled>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </td>
                            <?php
                        }

                        if (!$isview) {
                            ?>
                            <td>
                                <?php
                                # --- copy ----
                                if ($f_result[$gtabid]["data_type"][$key] != 22 and $f_result[$gtabid]["field_type"][$key] != 14 and $f_result[$gtabid]["field_type"][$key] != 15 and $f_result[$gtabid]["field_type"][$key] < 100) {
                                    if ($l_result[$gtabid]["copy"][$key] or !$l_result) {
                                        ?>
                                        <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                            <input class="form-check-input m-0"
                                                   type="checkbox"
                                                   name="<?= "copyrule" ?>_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                                   onclick="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]["field_id"][$key] ?>',<?= 13 ?>)" <?= $f_result[$gtabid]["copy"][$key] ? "checked" : "" ?>>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                            <input
                                                    class="form-check-input opacity-50"
                                                    type="checkbox"
                                                    readonly
                                                    disabled>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </td>
                            <?php
                        }

                        if (!$isview) {
                            ?>
                            <td>
                                <?php
                                # --- list edit ----
                                $noListEdit = array(10, 18, 22, 31, 32, 34, 35, 36, 37, 39, 45, 46);
                                if (!in_array($f_result[$gtabid]["data_type"][$key], $noListEdit)) {
                                    if ($l_result[$gtabid]["listedit"][$key] or !$l_result) {
                                        ?>
                                        <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                            <input class="form-check-input m-0"
                                                   type="checkbox"
                                                   name="<?= "listeditrule" ?>_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                                   onclick="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]["field_id"][$key] ?>',<?= 33 ?>)" <?= $f_result[$gtabid]["listedit"][$key] ? "checked" : "" ?>>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                            <input
                                                    class="form-check-input opacity-50"
                                                    type="checkbox"
                                                    readonly
                                                    disabled>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </td>
                            <?php
                        }

                        # --- option ----
                        ?>
                        <td>
                            <?php
                            if ($f_result[$gtabid]["field_type"][$key] == 2 or $f_result[$gtabid]["field_type"][$key] == 21 or $f_result[$gtabid]["data_type"][$key] == 42 or $f_result[$gtabid]["data_type"][$key] == 30 or $f_result[$gtabid]["data_type"][$key] == 28 or $f_result[$gtabid]["data_type"][$key] == 29 or $f_result[$gtabid]["field_type"][$key] == 4 or $f_result[$gtabid]["field_type"][$key] == 11 or $f_result[$gtabid]["field_type"][$key] == 19) {
                                if ($l_result[$gtabid]["option"][$key] or !$l_result) {
                                    ?>
                                    <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                        <input class="form-check-input m-0"
                                               type="checkbox"
                                               name="<?= "optionrule" ?>_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                               onclick="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]["field_id"][$key] ?>',<?= 32 ?>)" <?= $f_result[$gtabid]["option"][$key] ? "checked" : "" ?>>
                                    </div>
                                    <?php
                                } else {
                                    ?>
                                    <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                        <input
                                                class="form-check-input opacity-50"
                                                type="checkbox"
                                                readonly
                                                disabled>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </td>
                        <?php

                        if (!$isview) {
                            ?>
                            <td>
                                <?php
                                # --- speech recognition ----
                                if (in_array($f_result[$gtabid]["data_type"][$key], array(1 /* add more here */))) {
                                    if ($l_result[$gtabid]["speechrec"][$key] or !$l_result) {
                                        ?>
                                        <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                            <input class="form-check-input m-0"
                                                   type="checkbox"
                                                   name="<?= "speechrecrule" ?>_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                                   onclick="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]["field_id"][$key] ?>',<?= 34 ?>)" <?= $f_result[$gtabid]["speechrec"][$key] ? "checked" : "" ?>>
                                        </div>
                                        <?php
                                    } else {
                                        ?>
                                        <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                            <input
                                                    class="form-check-input opacity-50"
                                                    type="checkbox"
                                                    readonly
                                                    disabled>
                                        </div>
                                        <?php
                                    }
                                }
                                ?>
                            </td>
                            <?php
                        }

                        # --- versioning ----
                        if (!$isview and $gtab["versioning"][$gtabid] and $f_result[$gtabid]["versioning_type"] == 2) {
                            ?>
                            <td>
                                <?php
                                if ($f_result[$gtabid]["data_type"][$key] != 22 and $f_result[$gtabid]['field_type'][$key] != 14 and $f_result[$gtabid]["field_type"][$key] != 15 and $f_result[$gtabid]['field_type'][$key] < 100) {
                                    #if($l_result[$gtabid]["versionable"][$key]){
                                    #	echo "<INPUT TYPE=\"checkbox\" readonly disabled checked style=\"opacity:0.3;filter:Alpha(opacity=30);\"></td>";
                                    #}else{
                                    ?>
                                    <div class="form-check align-middle d-flex justify-content-center p-0 h-100">
                                        <input class="form-check-input m-0"
                                               type="checkbox"
                                               name="<?= "versionrule" ?>_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                               onclick="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]["field_id"][$key] ?>',<?= 16 ?>)" <?= $f_result[$gtabid]["versionable"][$key] == 1 ? "checked" : "" ?>>
                                    </div>
                                    <?php
                                    #}
                                }
                                ?>
                            </td>
                            <?php
                        }

                        # --- color ----
                        ?>
                        <td STYLE="cursor:pointer;">
                            <div class="d-flex justify-content-center">
                                <div ID="color_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                     onclick="div4(this,'<?= $gtabid ?>','<?= $f_result[$gtabid]["field_id"][$key] ?>')"
                                     STYLE="border:1px solid <?= $farbschema["WEB3"] ?>;width:15px;height:15px; <?= $f_result[$gtabid]["color"][$key] ? "background-color:" . $f_result[$gtabid]['color'][$key] . ";" : "" ?>">
                                </div>
                            </div>
                        </td>

                        <?php
                        # --- default ----
                        if (!$isview) {
                            ?>
                            <td>
                                <?php
                                if ($f_result[$gtabid]["field_type"][$key] < 100) {
                                    ?>
                                    <input
                                            class="form-control form-control-sm"
                                            type="text"
                                            name="filterdefault_<?= $gtabid ?>_<?= $f_result[$gtabid]['field_id'][$key] ?>"
                                            value="<?= htmlentities($f_result[$gtabid]["def"][$key], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) ?>"
                                            onchange="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]['field_id'][$key] ?>',12)">
                                    <?php
                                }
                                ?>
                            </td>
                            <?php
                        }

                        # ----------- Filter ---------------
                        if ($f_result[$gtabid]["filtertyp"][$key] == 1) {
                            $st = "style=\"color:red;cursor:pointer;\" title=\"use automatic filterrule set by filter\"";
                        } else {
                            $st = "style=\"cursor:pointer;\" title=\"use manual filterrule [ = '\$abc' ] or [ < 23 ]\"";
                        }

                        # --------------------- Hiddenfeld Values -------------------
                        $hfieldval = "";
                        for ($i = 0; $i <= 2; $i++) {
                            if ($gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]][$i] or $gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]][$i] == "0") {
                                if ($searchlang['andor'][$gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]]['andor'][$i]]) {
                                    $hfieldval .= " " . htmlentities($searchlang["andor"][$gsr[$gtabid][$f_result[$gtabid]["field_id"][$key]]["andor"][$i]], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) . " ";
                                }
                                $hfieldval .= "(";
                                if ($searchlang['num'][$gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]]['num'][$i]]) {
                                    $hfieldval .= htmlentities($searchlang["num"][$gsr[$gtabid][$f_result[$gtabid]["field_id"][$key]]["num"][$i]], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) . " group_tabrows.php";
                                }
                                $hfieldval .= "'" . htmlentities($gsr[$gtabid][$f_result[$gtabid]["field_id"][$key]][$i], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) . "'";
                                if ($searchlang['txt'][$gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]]['txt'][$i]]) {
                                    $hfieldval .= " " . htmlentities($searchlang["txt"][$gsr[$gtabid][$f_result[$gtabid]["field_id"][$key]]["txt"][$i]], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]);
                                }
                                if ($searchlang['cs'][$gsr[$gtabid][$f_result[$gtabid]['field_id'][$key]]['cs'][$i]]) {
                                    $hfieldval .= " " . htmlentities($searchlang["cs"][$gsr[$gtabid][$f_result[$gtabid]["field_id"][$key]]["cs"][$i]], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]);
                                }
                                $hfieldval .= ")";
                            }
                        }

                        # --------------------- Inputfeld Values -------------------
                        $ifieldval = "";
                        $gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]] = unserialize($f_result[$gtabid]["filter"][$key]);
                        if ($gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]]) {
                            for ($i = 0; $i <= 2; $i++) {
                                if ($gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]][$i] or $gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]][$i] == "0") {
                                    if ($searchlang['andor'][$gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]]['andor'][$i]]) {
                                        $ifieldval .= " " . htmlentities($searchlang["andor"][$gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]]["andor"][$i]], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) . " ";
                                    }
                                    $ifieldval .= "(";
                                    if ($searchlang['num'][$gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]]['num'][$i]]) {
                                        $ifieldval .= htmlentities($searchlang["num"][$gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]]["num"][$i]], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) . " group_tabrows.php";
                                    }
                                    $ifieldval .= "'" . htmlentities($gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]][$i], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]) . "'";
                                    if ($searchlang['txt'][$gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]]['txt'][$i]]) {
                                        $ifieldval .= " " . htmlentities($searchlang["txt"][$gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]]["txt"][$i]], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]);
                                    }
                                    if ($searchlang['cs'][$gsh[$gtabid][$f_result[$gtabid]['field_id'][$key]]['cs'][$i]]) {
                                        $ifieldval .= " " . htmlentities($searchlang["cs"][$gsh[$gtabid][$f_result[$gtabid]["field_id"][$key]]["cs"][$i]], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]);
                                    }
                                    $ifieldval .= ")";
                                }
                            }
                        } else {
                            $ifieldval .= htmlentities($f_result[$gtabid]["filter"][$key], ENT_QUOTES, $GLOBALS["umgvar"]["charset"]);
                        }
                        ?>
                        <td>
                            <?php # --------------------- Hiddenfeld ------------------- ?>
                            <input
                                    type="hidden"
                                <?= $st ?>
                                    name="filterprev_<?= $gtabid ?>_<?= $f_result[$gtabid]['field_id'][$key] ?>"
                                    value="<?= $hfieldval ?>">

                            <?php # --------------------- Inputfeld ------------------- ?>
                            <textarea
                                    class=""
                                    rows="1"
                                <?= $st ?>
                                type="text"
                                    name="filterrule_<?= $gtabid ?>_<?= $f_result[$gtabid]['field_id'][$key] ?>"
                                    onchange="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]['field_id'][$key] ?>',8)"><?= $ifieldval ?></textarea>
                        </td>
                        <?php


                        if (!$isview) {
                            # ----------- Editrules ---------------
                            ?>
                            <td>
                            <textarea
                                    class=""
                                    rows="1"
                                    type="text"
                                    name="editrule_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                    onchange="save_rules(<?= $gtabid ?>,'',27)"><?= $f_result[$gtabid]["editrule"][$key] ?></textarea>
                            </td>
                            <?php
                            # --- trigger ----
                            if ($gtrigger[$gtabid]) {
                                ?>
                                <td>
                                    <select
                                            NAME="triggerrule_<?= $gtabid ?>_<?= $key ?>[]"
                                            STYLE="width:120px;"
                                            onchange="save_rules('<?= $gtabid ?>','<?= $key ?>',14)">
                                        <option VALUE=""></option>
                                        <?php
                                        $trlist = array();
                                        foreach ($gtrigger[$gtabid]["id"] as $trid => $trval) {
                                            if ($gtrigger[$gtabid]["type"][$trid] == "UPDATE") {
                                                if (in_array($trid, $f_result[$gtabid]["field_trigger"][$key])) {
                                                    $SELECTED = "SELECTED";
                                                    $trlist[] = $gtrigger[$gtabid]["trigger_name"][$trid];
                                                } else {
                                                    $SELECTED = "";
                                                }
                                                ?>
                                                <option VALUE="<?= $trid ?>" <?= $SELECTED ?>>
                                                    <?= $gtrigger[$gtabid]["trigger_name"][$trid] ?>
                                                    (<?= $gtrigger[$gtabid]["type"][$trid] ?>)
                                                </option>
                                                <?php
                                            }
                                        }
                                        ?>
                                    </select>
                                </td>
                                <?php
                            }
                        }

                        # --- number-format ----
                        if ($f_result[$gtabid]['field_type'][$key] == 5 or $f_result[$gtabid]['field_type'][$key] == 2) {
                            ?>
                            <td>

                                <input
                                        class="form-control form-control-sm"
                                        type="text"
                                        value="<?= $f_result[$gtabid]["nformat"][$key] ?>"
                                        name="filterformat_<?= $gtabid ?>_<?= $f_result[$gtabid]['field_id'][$key] ?>"
                                        onchange="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]['field_id'][$key] ?>',10)">

                                <?php
                                # ---- Währung ----------
                                if ($f_result[$gtabid]['data_type'][$key] == 30) {
                                ?>
                                <select
                                        class="form-select form-select-sm"
                                        name="filtercurrency_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                        onchange="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]['field_id'][$key] ?>',11)">
                                    <option value=""></option>
                                    <?php
                                    asort($lmcurrency['currency']);
                                    foreach ($lmcurrency['currency'] as $ckey => $cval) {
                                        ?>
                                        <option
                                                value="<?= $lmcurrency['code'][$ckey] ?>"
                                            <?= $lmcurrency['code'][$ckey] == $f_result[$gtabid]["currency"][$key] ? "selected" : "" ?>>
                                            <?= $lmcurrency['currency'][$ckey] ?>
                                        </option>
                                        <?php
                                    }
                                    }
                                    ?>
                            </td>
                            <?php
                        } else {
                            ?>
                            <td></td>
                            <?php
                        }


                        # --- extension-type ----
                        ?>
                        <td>
                            <select class="form-select form-select-sm"
                                    name="filterextension_<?= $gtabid ?>_<?= $f_result[$gtabid]["field_id"][$key] ?>"
                                    onchange="save_rules('<?= $gtabid ?>','<?= $f_result[$gtabid]["field_id"][$key] ?>',15)">
                                <option value=" "></option>
                                <?php foreach ($ext_fk as $key1 => $value1): ?>
                                    <option value="<?= $value1 ?>"<?= $f_result[$gtabid]["ext_type"][$key] == $value1 ? "selected" : "" ?>>
                                        <?= $value1 ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    <?php
                }
            }
            ?>
        </table>
    </div>

<?php



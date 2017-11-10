<?php

function easy_options($params, &$smarty)
{

    if (isset($params["options"]) && is_array($params["options"])) {
        $options = $params["options"];
    }

    if (!function_exists("smarty_function_html_options")) {
        require $smarty->_get_plugin_filepath("function", "html_options");
    }
    return smarty_function_html_options($params, $smarty);
}
?>
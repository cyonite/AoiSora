<?php

class WpScriptIncludes extends WpFrontIncludes
{
    function enqueueInclude(FrontInclude $script) {
        wp_enqueue_script($script->getHandle(),$script->getSrc(),$script->getDependency(),$script->getVersion(),$script->loadInFooter());
    }

    function registerInclude(FrontInclude $script) {
        wp_register_script($script->getHandle(),$script->getSrc(),$script->getDependency(),$script->getVersion(),$script->loadInFooter());
    }

    function dequeueInclude($includeHandle) {
        wp_dequeue_script($includeHandle);
    }

    function deregisterInclude($includeHandle) {
        wp_deregister_script($includeHandle);
    }
}

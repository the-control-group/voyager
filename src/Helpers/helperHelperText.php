<?php

if (!function_exists('outputAriaForHelperText')) {
    /**
     * If row has helperText, output the appropriate aria-described by tag for the input.
     *
     * @param DataRow $row
     *
     * @return string HTML output.
     */
    function outputAriaForHelperText($row)
    {
        return isset($row->details->helperText) ? 'aria-describedby="'.$row->field.'HelpBlock"' : '';
    }
}

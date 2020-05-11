<?php
/**
 * Helper class for Table Dump module
 * 
 * @package    Table Dump
 * @subpackage Modules
 * @link http://docs.joomla.org/J3.x:Creating_a_simple_module/Developing_a_Basic_Module
 * @license        GNU/GPL, see LICENSE.php
 * mod_tabledump is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */
class ModTableDumpHelper
{
    /**
     * Retrieves one row of record column data and outputs a table with the column data filled into the user template
     *
     * @param   array  $params An object containing the module parameters
     *
     * @access public
     */    
    public static function getTableDump($params)
    {
        // Obtain a database connection.
        $db = JFactory::getDbo();

        // Retrieve the various parameters
        $fieldnames = explode(",",preg_replace("/\s+/",",",$params->get('fieldnames')));
        $tablequery = $params->get('tablequery');
        $errormessage = $params->get('errormessage');
        $tableprefix = $params->get('tableprefix','<table>');
        $tablehead = $params->get('tablehead');
        $tablerow = $params->get('tablerow');
        $tablefoot = $params->get('tablefoot','</tbody></table>');
        $hardspaces = $params->get('hard_spaces');
        $groupby = trim($params->get('groupby'));
        $grouphead = $params->get('grouphead');
        $hard_spaces = $params->get('hard_spaces');

        // Prepare the query
        $db->setQuery($tablequery);

        // Load the row.
        try
        {
            $records = $db->loadObjectList();
        }
        catch (Exception $e)
        {
            $tablehtml = $errormessage;
        }

        // Replace tags in rows
        $lastgroup = "";
        $thisgrouphead = "";
        if ($records)
        {
            // Iterate over every row in the database recordset returned.
            foreach ($records as $record)
            {
                // See if we need a group header and if it's time to output one.
                if ($groupby)
                {
                    $thisgroup = $record->$groupby;
                    $thisgrouphead = "";
                    if ($thisgroup != $lastgroup)
                    {
                        if ($hard_spaces)
                            $thisgrouphard = html_entity_decode(str_replace(array(" ","-","\n"),array("&nbsp;","&#8209;","<br/>"),$thisgroup),ENT_QUOTES);
                        else
                            $thisgrouphard = $thisgroup;

                        $thisgrouphead = html_entity_decode(str_replace("{".$groupby."}",$thisgrouphard,$grouphead),ENT_QUOTES);
                    }
                    $lastgroup = $thisgroup;
                }

                // Get the HTML for the next row ready.
                $thisrow = $tablerow;

                // Go through the list of field names and replace each {fieldname} in the user-provided HTML for this row.
                foreach ($fieldnames as $fieldname)
                {
                    $fielddata = $record->$fieldname;
                    if ($hard_spaces) $fielddata = html_entity_decode(str_replace(array(" ","-","\n"),array("&nbsp;","&#8209;","<br/>"),$fielddata),ENT_QUOTES);
                    $thisrow = html_entity_decode(str_replace("{".$fieldname."}",$fielddata,$thisrow),ENT_QUOTES);
                }
                // Add the processed row to the growing structure.
                $tablehtml .= $thisgrouphead.$thisrow;
            }
        }
        // Replace the special tags.
		if (!$groupby)
			$tableprefix = html_entity_decode(str_replace("{tablerows}",count($records),$tableprefix),ENT_QUOTES);
        $tablefoot = html_entity_decode(str_replace("{tablerows}",count($records),$tablefoot),ENT_QUOTES);

        // Return the table with all fieldname substitutions made and footer appended.
        return $tableprefix.$tablehead.$tablehtml.($tablefoot ? $tablefoot : "</tbody></table>");
    }
}
?>
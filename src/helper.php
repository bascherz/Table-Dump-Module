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
        $tablehtml = $params->get('tablehead');
        $tablerow = $params->get('tablerow');
        $tablefoot = $params->get('tablefoot');

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
        // Replace the special tags.
        $tablehtml = html_entity_decode(str_replace("{tablerows}",count($records),$tablehtml),ENT_QUOTES);
        $tablefoot = html_entity_decode(str_replace("{tablerows}",count($records),$tablefoot),ENT_QUOTES);
        if ($records)
        {
            // Iterate over every row in the database recordset returned.
            foreach ($records as $record)
            {
                // Get the HTML for the next row ready.
                $thisrow = $tablerow;

                // Go through the list of field names and replace each {fieldname} in the user-provided HTML for this row.
                foreach ($fieldnames as $fieldname)
                {
                    $thisrow = html_entity_decode(str_replace("{".$fieldname."}",$record->$fieldname,$thisrow),ENT_QUOTES);
                }
                // Add the processed row to the growing structure.
                $tablehtml .= $thisrow;
            }
        }
        // Return the table with all fieldname substitutions made and footer appended.
        return $tablehtml.($tablefoot ? $tablefoot : "</tbody></table>");
    }
}
?>
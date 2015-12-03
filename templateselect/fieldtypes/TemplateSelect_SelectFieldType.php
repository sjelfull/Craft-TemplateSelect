<?php
namespace Craft;

class TemplateSelect_SelectFieldType extends BaseFieldType
{
    public function getName()
    {
        return Craft::t('Template Select');
    }

    public function defineContentAttribute()
    {
        return AttributeType::String;
    }

    public function getInputHtml($name, $value)
    {
        // Get site templates path
        $templatesPath = $siteTemplatesPath = craft()->path->getSiteTemplatesPath();


        // Check if the templates path is overriden by configuration
        // TODO: Normalize path
        $limitToSubfolder = craft()->config->get('templateselectSubfolder');
        if ($limitToSubfolder) $templatesPath = $templatesPath . rtrim($limitToSubfolder, '/') . '/';

        // Check if folder exists, or give error
        if (! IOHelper::folderExists($templatesPath) ) throw new \InvalidArgumentException('(Template Select) Folder doesn\'t exist: ' . $templatesPath);

        // Get folder contents
        $templates = IOHelper::getFolderContents($templatesPath, TRUE);

        // Add placeholder for when there is no template selected
        $filteredTemplates = array('' => Craft::t('No template selected'));

        // Turn array into ArrayObject 
        $templates = new \ArrayObject($templates);

        // Iterate over template list
        // * Remove full path 
        // * Remove folders from list
        for ($list = $templates->getIterator();
            $list->valid(); $list->next()) 
            { 
                $filename = $list->current();
                
                $filename = str_replace(realpath($templatesPath), '', $filename);
                $filenameIncludingSubfolder = ($limitToSubfolder) ? $limitToSubfolder . $filename : $filename;
                $isTemplate = preg_match("/(.html|.twig)$/u", $filename);
                
                if ($isTemplate) $filteredTemplates[$filenameIncludingSubfolder] = $filename;
            }

        // Render field
        return craft()->templates->render('_includes/forms/select', array(
            'name'    => $name,
            'value'   => $value,
            'options' => $filteredTemplates,
        ));
    }

}
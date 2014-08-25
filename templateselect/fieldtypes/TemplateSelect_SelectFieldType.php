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
        $templatesPath = craft()->path->getSiteTemplatesPath();

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
                
                $filename = str_replace($templatesPath, '', $filename);
                $isTemplate = preg_match("/(.html|.twig)$/u", $filename);
                
                if ($isTemplate) $filteredTemplates[$filename] = $filename;
            }

        // Render field
        return craft()->templates->render('_includes/forms/select', array(
            'name'    => $name,
            'value'   => $value,
            'options' => $filteredTemplates,
        ));
    }

}
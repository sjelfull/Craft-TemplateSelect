<?php
namespace Craft;

class TemplateSelect_SelectFieldType extends BaseFieldType
{
    private $templatePaths = [];
    private $filteredTemplates = [];
    
    public function __construct()
    {
        $this->filteredTemplatesarray[''] = Craft::t('No template selected');
    }

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
        $this->getTemplatePaths();
        $this->getTemplates();

        // Render field
        return craft()->templates->render('_includes/forms/select', array(
            'name'    => $name,
            'value'   => $value,
            'options' => $this->filteredTemplates,
        ));
    }
    
    private function getTemplatePaths()
    {

        // Get site templates path
        $templatesPath = $siteTemplatesPath = craft()->path->getSiteTemplatesPath();

        // Check if the templates path is overriden by configuration
        // TODO: Normalize path
        $limitToSubfolder = craft()->config->get('templateselectSubfolder');

        if ($limitToSubfolder) {
            if (is_array($limitToSubfolder)) {
                foreach ($limitToSubfolder as $subFolder) {
                    $templatePath = $templatesPath . rtrim($subFolder, '/') . '/';
                    
                    $this->validateTemplatePath($templatePath);
                    
                    $this->templatePaths[] = $templatePath;
                }
            } else {
                $templatePath = $templatesPath . rtrim($limitToSubfolder, '/') . '/';
                
                $this->validateTemplatePath($templatePath);
                
                $this->templatePaths[] = $templatePath;
            }
        }
        
        return;
    }
    
    private function getTemplates()
    {
        foreach ($this->templatePaths as $templatesPath) {
         
            // Get folder contents
            $templates = IOHelper::getFolderContents($templatesPath, true);

            // Turn array into ArrayObject
            $templates = new \ArrayObject($templates);

            // Iterate over template list
            // * Remove full path
            // * Remove folders from list
            for ($list = $templates->getIterator(); $list->valid(); $list->next()) {
                $filename = $list->current();
                
                $filename = str_replace(str_replace("\\", "/", realpath($templatesPath)), '', $filename);
                $filenameIncludingSubfolder = ($limitToSubfolder) ? $limitToSubfolder . $filename : $filename;
                $isTemplate = preg_match("/(.html|.twig)$/u", $filename);
                
                if ($isTemplate) {
                    $this->filteredTemplates[$filenameIncludingSubfolder] = $filename;
                }
            }
        }
    }
    
    private function validateTemplatePath($templatesPath)
    {
        if (! IOHelper::folderExists($templatesPath)) {
            throw new \InvalidArgumentException('(Template Select) Folder doesn\'t exist: ' . $templatesPath);
        }
    }
}

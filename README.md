## Template Select for Craft

A fieldtype that allows you to select a template from a dropdown.

## Install 

* [Download the zip](https://github.com/sjelfull/Craft-TemplateSelect/archive/master.zip)
* Unzip in your craft/plugins folder
* Make sure the plugin folder is called **templateselect**

## Usage

If you want to include a template, you may do it like this in your template:

`{% include entry.fieldHandle %}`

## Limit to subfolder

By using the config setting **templateselectSubfolder**, you can limit the list to a subfolder of your templates folder.

In general.php, add this line:

̀`
"templateselectSubfolder" => "subfolder"
`

Alternatively you can provide an array to include multiple folders

`
"templateselectSubfolder" => ["subfolder1", "subfolder2"]
`
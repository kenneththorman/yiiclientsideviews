<?php
/**
 * ClientsideViews class file.
 * @author Kenneth Thorman (kenneth.thorman@appinux.com)
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */

/**
 * ClientsideViews application component.
 * Used for registering ClientsideViews core functionality.
 */
class ClientsideViews extends CApplicationComponent {

    /**
     * @var boolean whether to register jQuery and the ClientsideViews JavaScript.
     */
    public $enableJS = true;

    protected $_assetsUrl;
    protected $_assetsPath;

    /**
     * Initializes the component.
     */
    public function init( ) {

        if( !Yii::getPathOfAlias( 'clientsideviews' ) )
            Yii::setPathOfAlias( 'clientsideviews', realpath( dirname( __FILE__ ).'/..' ) );
        
        $generatedTemplateFile = Yii::getPathOfAlias( 'clientsideviews.assets.javascripts' ) . DIRECTORY_SEPARATOR . 'mustache.tpl.js';
        if (!is_file($generatedTemplateFile)) {
            $this->refreshMustacheTemplates();            
        }
        
        if( $this->enableJS ) {
            $this->registerJs( );
        }

        parent::init();
    }

    /**
     * Registers the core JavaScript plugins.
     * @since 0.9.8
     */
    public function registerJs( ) {
		
		Yii::app( )->clientScript->registerCoreScript( 'jquery' );
        $this->registerScriptFile( 'ICanHaz.min.js' );
        $this->registerScriptFile( 'mustache.tpl.js' );
    }

    /**
     * Registers a JavaScript file in the assets folder.
     * @param string $fileName the file name.
     * @param integer $position the position of the JavaScript file.
     */
    public function registerScriptFile( $fileName, $position = CClientScript::POS_END ) {
        Yii::app( )->clientScript->registerScriptFile( $this->getAssetsUrl( ).DIRECTORY_SEPARATOR.$fileName, $position );
    }

    /**
     * Returns the URL to the published assets folder.
     * @return string the URL
     */
    protected function getAssetsUrl( ) {
        if( $this->_assetsUrl == null ) {
            $assetsPath = Yii::getPathOfAlias( 'clientsideviews.assets.javascripts' );
            $this->_assetsUrl = Yii::app( )->assetManager->publish( $assetsPath, false, -1, YII_DEBUG );
        }
        return $this->_assetsUrl;
    }
	
    public function refreshMustacheTemplates()
    {
        /* 
        Find all files recursivly in the basepath/protected named mustache.tpl
        Foreach files add to js array with a name based on the directory path and filename without 
        mustache.tpl
        
        */
        $basePath = Yii::app()->basePath;
        $templates = array();
        $options=  array('fileTypes'=>array('tpl'));
        $templateFiles = CFileHelper::findFiles(realpath(Yii::app()->basePath),$options);
        foreach($templateFiles as $file){
            // stupid additional check due to the findFiles function cannot handle . seperated filenames
            if (strpos($file,'mustache') !== false) {
                $templateId = str_replace(array($basePath,DIRECTORY_SEPARATOR,'mustache.tpl','.'),array('','_','',''),$file);
                array_push($templates, array(
                    'name' => $templateId,
                    'template' => $this->stripEndLine($this->readTemplate($file)))
                );
            }
        }

        $templatesJs = "$.each(".json_encode($templates).", function (index, template) {ich.addTemplate(template.name, template.template);});";
        $this->writeTemplateFile(Yii::getPathOfAlias( 'clientsideviews.assets.javascripts' ), $templatesJs);        
	}
    
    private function writeTemplateFile($path,$fileContents)
    {
        $my_file = $path. DIRECTORY_SEPARATOR . 'mustache.tpl.js';
        $handle = fopen($my_file, 'w') or die('Cannot open file:  '.$my_file);
        fwrite($handle, $fileContents);
        fclose($handle);
    }
        
    private function readTemplate($file)
    {

        $handle = fopen($file, 'r');
        $data = fread($handle,filesize($file));
        fclose($handle);
        return $data;
    }
    
    private function stripEndLine($template)
    {
        $output = str_replace(array("\r\n", "\r"), "\n", $template);
        $lines = explode("\n", $output);
        $new_lines = array();

        foreach ($lines as $i => $line) {
            if(!empty($line))
                $new_lines[] = trim($line);
        }
        return implode($new_lines);        
    }
	
}

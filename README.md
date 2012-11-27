yiiclientsideviews
==================

Yii Framework: Clientside Views Extension

For background for this extension
http://kenneththorman.blogspot.dk/2012/11/high-performance-cachable-websites-web.html


Create a folder named /protected/extensions/clientsideviews

Copy the files from this repository to the /protected/extensions/clientsideviews folder

Add the following to the /protected/config/main.php


<pre>
return array(
  ...
  'preload'=>array(
    ...
    'clientsideviews',
  ),
  ...
  'components'=>array(
    "class" => "ext.clientsideviews.components.ClientsideViews"
  ),
...
);
</pre>

Now you should be able to start writing clientside mustache templates for rendering.
There might be some problems with ' (singlequotes) in the template sources so try to stay away from these
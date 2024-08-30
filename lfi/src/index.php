<!DOCTYPE html>
<head>
    <html>

    </html>
    <body class="u-body">
  
        <?php
            error_reporting(0);
            $file=$_GET["p"];
            //$file=preg_replace('/[^a-zA-Z0-9_]/' , '' , $file);
            //include($file);

            if(isset($file)){
                include("$file");
            }
            else
            {
                include("index.html");
            }
        ?>
    </body>
</head>
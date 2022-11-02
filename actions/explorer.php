

<html>

    <head>
        <title>Explorer</title>
        <link href="bootstrap/bootstrap-slate.css" rel="stylesheet" media="screen"/>
    </head>

    <body onLoad="window.close();" style="background-color: 0">


        <?php
        require_once '../lib/functions-helper.php';

        
        
        $repertoire = Helper_var::get_var('repertoire', '');

        //var_dump($repertoire);
        
        $args = strlen($repertoire) > 0 ? ' /root,"' . str_replace('/', '\\', $repertoire) . '"' : '';
        //$args = strlen($repertoire) > 0 ? ' "' . str_replace('/', '\\', $repertoire) . '"' : '';
        $cmd = 'explorer.exe ' . $args;

        print_r($cmd."\n");
        
        $ret = shell_exec($cmd);
        //
        //$ret = shell_exec('test.bat');
        
        
        
        echo str_ireplace("\n", "<br>", $ret)
        

        ?>

    </body>

</html>


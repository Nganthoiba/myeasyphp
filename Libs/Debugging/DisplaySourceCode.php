<?php
namespace MyEasyPHP\Libs\Debugging;

/**
 * Description of DisplaySourceCode
 * This file is only meant for displaying certain lines of source code where error occurs.
 * This file is used only when it is in development mode.
 * @author Nganthoiba
 */
class DisplaySourceCode {
    private static function highlightText($text, $fileExt="")
    {
        if ($fileExt == "php")
        {
            ini_set("highlight.comment", "#008000");
            ini_set("highlight.default", "#000000");
            ini_set("highlight.html", "#808080");
            ini_set("highlight.keyword", "#0000BB; font-weight: bold");
            ini_set("highlight.string", "#DD0000");
        }
        else if ($fileExt == "html")
        {
            ini_set("highlight.comment", "green");
            ini_set("highlight.default", "#CC0000");
            ini_set("highlight.html", "#000000");
            ini_set("highlight.keyword", "black; font-weight: bold");
            ini_set("highlight.string", "#0000FF");
        }
        // ...

        $text = ($text);
        $text = highlight_string("<?php " . $text, true);  // highlight_string() requires opening PHP tag or otherwise it will not colorize the text
        $text = trim($text);
        $text = preg_replace("|^\\<code\\>\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>|", "", $text, 1);  // remove prefix
        $text = preg_replace("|\\</code\\>\$|", "", $text, 1);  // remove suffix 1
        $text = trim($text);  // remove line breaks
        $text = preg_replace("|\\</span\\>\$|", "", $text, 1);  // remove suffix 2
        $text = trim($text);  // remove line breaks
        $text = preg_replace("|^(\\<span style\\=\"color\\: #[a-fA-F0-9]{0,6}\"\\>)(&lt;\\?php&nbsp;)(.*?)(\\</span\\>)|", "\$1\$3\$4", $text);  // remove custom added "<?php "

        return $text;
    }
    /*
    $file : the file to be displayed
     * $lineNoFrom: The line number of the file from where it should start displaying
     * $lineNoTo:   The line number of the file from where it should stop displaying
     * $markLineNo: The line number of the file where it will mark for displaying error.
     *              Default value -1 will mean it is not going to mark anything.
     * 
     *      */
    public static function display($file, int $lineNoFrom, int $lineNoTo, int $markLineNo=-1){
        $lines = file($file);//file in to an array
        $totalLines = sizeof($lines);
        //echo count($lines); //line 2
        $lineNoTo = ($lineNoTo > $totalLines)?$totalLines:$lineNoTo;
        $i = $lineNoFrom-1;
        //foreach($lines as $line){
        for($j=$i; $j < $lineNoTo; $j++) { 
            $style = ($j===($markLineNo-1))?'style="background-color:#d2c5c8"':'';            
            echo '<div '.$style.'>'.(++$i).' '. self::highlightText($lines[$j],'php').'</div><span style="display:none;">\n\t</span>';
        }
    }
}

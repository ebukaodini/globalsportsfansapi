<?php

register_shutdown_function( "fatal_handler" );

set_error_handler(function() {
   $backtraces = \debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT,10);
   
   for ($i = 0; $i < count($backtraces); $i++) {
      $backtrace = $backtraces[$i];
      if ($i > 0) {
         if (!empty($backtrace['file']) || !empty($backtrace['file'])) {
            $file = (string)$backtrace['file'] ?? "unknown file";
            $line = (string)$backtrace['line'] ?? "unknown line";
            
            if (DISPLAY_ERROR == true) {
               if (PHP_SAPI == 'cli') {
                  echo "   >> $file on line $line\n";
               } else {
                  echo "<span style='margin-left: 1em;'>&raquo; $file on line $line</span><br>";
               }
            }
            
            if (LOG_ERROR == true) {
               @error_log("\t>> $file on line $line\n", 3, LOG_FILE);
            }
         }
      } else {
         $code = (string)$backtrace['args'][0];
         $message = (string)$backtrace['args'][1];

         if (DISPLAY_ERROR == true) {
            if (PHP_SAPI == 'cli') {
               echo "[$code] $message\n\n";
            } else {
               echo sprintf("<strong>[%s] %s</strong><br><br>", $code, $message);
            }
         }

         if (LOG_ERROR == true) {
            @error_log("[" . date("D d-m-Y h:i:s A T") . "] [$code] $message\n", 3, LOG_FILE);
         }
         
         if (SEND_EMAIL_LOG == true) {
            $file = (string)$backtrace['args'][2] ?? "unknown file";
            $line = (string)$backtrace['args'][3] ?? "unknown line";
            @error_log("[" . date("D d-m-Y h:i:s A T") . "] [$code] $message in $file on line $line", 1, SEND_EMAIL_LOG_ADDRESS);
         }
      }
   }

   if (DISPLAY_ERROR == true) {
      echo (PHP_SAPI == 'cli') ? "\nA log of this error can be found at " . LOG_FILE . "\n" : "<br><small>A log of this error can be found at " . LOG_FILE . "</small><br><img align='top' width='25em' src='" . ASSETS_PATH . "imgs/favicon.ico'> InitFramework";
      exit;
   }
}, E_ALL);

function fatal_handler() {
    $errfile = "unknown file";
    $errstr  = "shutdown";
    $errno   = E_CORE_ERROR;
    $errline = 0;

    $error = error_get_last();

    if($error !== NULL) {
        $errno   = $error["type"];
        $errfile = $error["file"];
        $errline = $error["line"];
        $errstr  = $error["message"];

        exit("$errno, $errstr, $errfile, $errline");
    }
}
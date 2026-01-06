<?php
/**
 * Simple PHP 8.3 compatibility test for POP3 class
 *
 * This script verifies that the POP3 class can be instantiated
 * and that basic methods are callable without errors in PHP 8.3.
 */

require_once 'class.pop3.php';

echo "PHP Version: " . phpversion() . "\n";
echo "Testing POP3 class PHP 8.3 compatibility...\n\n";

// Test 1: Instantiation
echo "Test 1: Instantiating POP3 class... ";
try {
    $pop3 = new POP3();
    echo "PASS\n";
} catch (Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Verify properties are accessible
echo "Test 2: Checking public properties... ";
try {
    $pop3->POP3_PORT = 110;
    $pop3->POP3_TIMEOUT = 30;
    $pop3->CRLF = "\r\n";
    $pop3->do_debug = 0;
    $pop3->host = 'localhost';
    $pop3->port = 110;
    $pop3->tval = 30;
    $pop3->username = 'test';
    $pop3->password = 'test';
    echo "PASS\n";
} catch (Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Verify methods are callable (without actually connecting)
echo "Test 3: Checking method signatures... ";
try {
    // These should be callable even if they fail due to no connection
    $reflection = new ReflectionClass('POP3');

    // Check for required methods
    $requiredMethods = ['__construct', 'Authorise', 'Connect', 'Login', 'Disconnect'];
    foreach ($requiredMethods as $method) {
        if (!$reflection->hasMethod($method)) {
            throw new Exception("Method $method not found");
        }
    }

    echo "PASS\n";
} catch (Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Verify no deprecated constructs are used
echo "Test 4: Verifying no PHP 4/5 constructs... ";
try {
    // Read the source file and check for deprecated patterns
    $source = file_get_contents('class.pop3.php');

    // Check for old-style references
    if (preg_match('/array\(&\$this,/', $source)) {
        throw new Exception("Found deprecated &\$this reference in array");
    }

    // Check for socket_set_timeout (removed in PHP 7)
    if (preg_match('/socket_set_timeout/', $source)) {
        throw new Exception("Found deprecated socket_set_timeout function");
    }

    echo "PASS\n";
} catch (Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 5: Verify error handler callback is PHP 8 compatible
echo "Test 5: Testing error handler compatibility... ";
try {
    $reflection = new ReflectionClass('POP3');
    $catchWarning = $reflection->getMethod('catchWarning');
    $params = $catchWarning->getParameters();

    // Should have 4 parameters: errno, errstr, errfile, errline
    if (count($params) < 4) {
        throw new Exception("catchWarning should have at least 4 parameters");
    }

    echo "PASS\n";
} catch (Throwable $e) {
    echo "FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n";
echo "===================================\n";
echo "All PHP 8.3 compatibility tests PASSED!\n";
echo "===================================\n";
echo "\nNote: This test only verifies basic compatibility.\n";
echo "Actual POP3 functionality requires a POP3 server connection.\n";
echo "POP-before-SMTP is DEPRECATED - use SMTP AUTH instead.\n";

<?php
/**
 * Test script to verify SMTP class compatibility with PHP 8.3
 * This performs basic instantiation and method availability tests
 */

// Include the SMTP class
require_once __DIR__ . '/class.smtp.php';

echo "PHP Version: " . PHP_VERSION . "\n";
echo "Testing SMTP class compatibility...\n\n";

// Test 1: Class instantiation
echo "Test 1: Class instantiation... ";
try {
    $smtp = new SMTP();
    echo "✓ PASS\n";
} catch (Throwable $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 2: Check public properties
echo "Test 2: Public properties... ";
try {
    assert($smtp->SMTP_PORT === 25, "SMTP_PORT should default to 25");
    assert($smtp->CRLF === "\r\n", "CRLF should default to \\r\\n");
    assert($smtp->do_verp === false, "do_verp should default to false");
    assert($smtp->do_debug === 0, "do_debug should default to 0");
    echo "✓ PASS\n";
} catch (Throwable $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 3: Check method existence
echo "Test 3: Core methods exist... ";
try {
    $methods = [
        'Connect', 'StartTLS', 'Authenticate', 'Hello', 'Mail',
        'Recipient', 'Data', 'Reset', 'Quit', 'Close', 'Connected'
    ];

    foreach ($methods as $method) {
        assert(method_exists($smtp, $method), "Method $method should exist");
    }
    echo "✓ PASS\n";
} catch (Throwable $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 4: Check optional RFC methods
echo "Test 4: Optional RFC methods exist... ";
try {
    $methods = ['Expand', 'Help', 'Noop', 'Verify', 'Send', 'SendAndMail', 'SendOrMail', 'Turn'];

    foreach ($methods as $method) {
        assert(method_exists($smtp, $method), "Method $method should exist");
    }
    echo "✓ PASS\n";
} catch (Throwable $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 5: Test Connected() method returns false when not connected
echo "Test 5: Connected() returns false initially... ";
try {
    $result = $smtp->Connected();
    assert($result === false, "Connected() should return false when not connected");
    echo "✓ PASS\n";
} catch (Throwable $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 6: Test Turn() returns false as documented
echo "Test 6: Turn() returns false (not implemented)... ";
try {
    $result = $smtp->Turn();
    assert($result === false, "Turn() should return false (not implemented)");
    echo "✓ PASS\n";
} catch (Throwable $e) {
    echo "✗ FAIL: " . $e->getMessage() . "\n";
    exit(1);
}

// Test 7: No PHP warnings or deprecations
echo "Test 7: No PHP warnings or deprecations... ";
error_reporting(E_ALL);
$errorCount = 0;
set_error_handler(function($errno, $errstr) use (&$errorCount) {
    $errorCount++;
    echo "\n  Warning/Error: $errstr\n";
    return true;
});

// Create a new instance to trigger any constructor warnings
$smtp2 = new SMTP();
$smtp2->SMTP_PORT = 587;
$smtp2->do_debug = 1;

restore_error_handler();

if ($errorCount === 0) {
    echo "✓ PASS\n";
} else {
    echo "✗ FAIL: $errorCount warnings/errors detected\n";
    exit(1);
}

echo "\n✓ All tests passed!\n";
echo "SMTP class is compatible with PHP 8.3\n";

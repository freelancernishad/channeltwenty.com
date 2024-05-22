<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class MacController extends Controller
{
    public function getMacAddress()
    {
        echo shell_exec('which python3');
        return;
  // Specify the full path to the Python executable
  $pythonCommand = 'C:\Users\User\AppData\Local\Programs\Python\Python312\python.exe'; // Replace with the actual path on your system

  // Check if Python is installed
  $process = new Process([$pythonCommand, '--version']);
  $process->run();

  if (!$process->isSuccessful()) {
      Log::error('Python script error: ' . $process->getErrorOutput());
      return response()->json(['error' => 'Python is not installed or not found in PATH.'], 500);
  }

  // Run the get_mac.py script
  $process = new Process([$pythonCommand, base_path('get_mac.py')]);
  $process->run();

  if (!$process->isSuccessful()) {
      Log::error('Python script error: ' . $process->getErrorOutput());
      return response()->json(['error' => 'Failed to execute Python script.'], 500);
  }

  $macAddress = $process->getOutput();
  return response()->json(['mac_address' => trim($macAddress)]);
    }


    public function checkPython()
    {
      // Specify the full path to the Python executable
      $pythonCommand = 'C:\Users\User\AppData\Local\Programs\Python\Python312\python.exe'; // Replace with the actual path on your system

      // Check if Python is installed
      $process = new Process([$pythonCommand, '--version']);
      $process->run();

      if (!$process->isSuccessful()) {
          Log::error('Python script error: ' . $process->getErrorOutput());
          return response()->json(['error' => 'Python is not installed or not found in PATH.'], 500);
      }

      // Run the get_mac.py script
      $process = new Process([$pythonCommand, base_path('get_mac.py')]);
      $process->run();

      if (!$process->isSuccessful()) {
          Log::error('Python script error: ' . $process->getErrorOutput());
          return response()->json(['error' => 'Failed to execute Python script.'], 500);
      }

      $macAddress = $process->getOutput();
      return response()->json(['mac_address' => trim($macAddress)]);
    }

}

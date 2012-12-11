<?php

	namespace caspar\core\cli;

	/**
	 * CLI command class, caspar -> help
	 *
	 * @author Daniel Andre Eikeland <zegenie@zegeniestudios.net>
	 * @version 1.0
	 * @license http://www.opensource.org/licenses/mozilla1.1.php Mozilla Public License 1.1 (MPL 1.1)
	 * @package caspar
	 * @subpackage cli
	 */

	/**
	 * CLI command class, caspar -> help
	 *
	 * @package caspar
	 * @subpackage cli
	 */
	class CliHelp extends \caspar\core\CliCommand
	{

		protected function _setup()
		{
			$this->_command_name = 'help';
			$this->_description = "Prints out help information";
			$this->addOptionalArgument('command', "Show help for the command specified");
		}

		public function do_execute()
		{
			$this->cliEcho("Caspar CLI help\n", 'white', 'bold');

			if ($this->hasProvidedArgument('command'))
			{
				$namespace_command = explode(":", $this->getProvidedArgument('command'));
				$namespace_name = (count($namespace_command) == 2) ? $namespace_command[0] : 'caspar';
				$command = (count($namespace_command) == 2) ? $namespace_command[1] : $namespace_command[0];

				$commands = self::getAvailableCommands();

				if (array_key_exists($namespace_name, $commands) && array_key_exists($command, $commands[$namespace_name]))
				{
					$this->cliEcho("\n");
					$class = $commands[$namespace_name][$command];
					$this->cliEcho("Usage: ", 'white', 'bold');
					$this->cliEcho(self::getCommandLineName() . " ");
					if ($namespace_name != 'caspar')
					{
						$this->cliEcho($namespace_name.':', 'green', 'bold');
					}
					$this->cliEcho($class->getCommandName() . " ", 'green', 'bold');

					$hasArguments = false;
					foreach ($class->getRequiredArguments() as $argument => $description)
					{
						$this->cliEcho($argument . ' ', 'magenta', 'bold');
						$hasArguments = true;
					}
					foreach ($class->getOptionalArguments() as $argument => $description)
					{
						$this->cliEcho('[' . $argument . '] ', 'magenta');
						$hasArguments = true;
					}
					$this->cliEcho("\n");
					$this->cliEcho($class->getDescription(), 'white', 'bold');
					$this->cliEcho("\n\n");

					if ($hasArguments)
					{
						$this->cliEcho("Argument descriptions:\n", 'white', 'bold');
						foreach ($class->getRequiredArguments() as $argument => $description)
						{
							$this->cliEcho("  {$argument}", 'magenta', 'bold');
							if ($description != '')
							{
								$this->cliEcho(" - {$description}");
							}
							else
							{
								$this->cliEcho(" - No description provided");
							}
							$this->cliEcho("\n");
						}
						foreach ($class->getOptionalArguments() as $argument => $description)
						{
							$this->cliEcho("  [{$argument}]", 'magenta');
							if ($description != '')
							{
								$this->cliEcho(" - {$description}");
							}
							else
							{
								$this->cliEcho(" - No description provided");
							}
							$this->cliEcho("\n");
						}
						$this->cliEcho("\n");
						$this->cliEcho("Parameters must be passed either in the order described above\nor in the following format:\n");
						$this->cliEcho("--parameter_name=value", 'magenta');
						$this->cliEcho("\n\n");
					}
				}
				else
				{
					$this->cliEcho("\n");
					$this->cliEcho("Unknown command\n", 'red', 'bold');
					$this->cliEcho("Type " . self::getCommandLineName() . ' ', 'white', 'bold');
					$this->cliEcho('help', 'green', 'bold');
					$this->cliEcho(" for more information about the cli tool.\n\n");
				}
			}
			else
			{
				$this->cliEcho("Below is a list of available commands:\n");
				$this->cliEcho("Type ");
				$this->cliEcho(self::getCommandLineName() . ' ', 'white', 'bold');
				$this->cliEcho('help', 'green', 'bold');
				$this->cliEcho(' command', 'magenta');
				$this->cliEcho(" for more information about a specific command.\n\n");
				$commands = $this->getAvailableCommands();

				foreach ($commands as $namespace_name => $namespace_commands)
				{
					if ($namespace_name != 'caspar' && count($namespace_commands) > 0)
					{
						$this->cliEcho("\n{$namespace_name}:\n", 'green', 'bold');
					}
					ksort($namespace_commands, SORT_STRING);
					foreach ($namespace_commands as $command_name => $command)
					{
						if ($namespace_name != 'caspar') $this->cliEcho("  ");
						$this->cliEcho("{$command_name}", 'green', 'bold');
						$this->cliEcho(" - {$command->getDescription()}\n");
					}

					if (count($commands) > 1 && $namespace_name == 'remote')
					{
						$this->cliEcho("\nModule commands, use ");
						$this->cliEcho("module_name:command_name", 'green');
						$this->cliEcho(" to run:");
					}
				}

				$this->cliEcho("\n");
			}
		}

	}
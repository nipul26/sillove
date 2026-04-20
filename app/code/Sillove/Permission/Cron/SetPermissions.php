<?php
namespace Sillove\Permission\Cron;

use Magento\Framework\App\Filesystem\DirectoryList;
use Psr\Log\LoggerInterface;

class SetPermissions
{
    /**
     * @var DirectoryList
     */
    protected $directoryList;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param DirectoryList $directoryList
     * @param LoggerInterface $logger
     */
    public function __construct(
        DirectoryList $directoryList,
        LoggerInterface $logger
    ) {
        $this->directoryList = $directoryList;
        $this->logger = $logger;
    }

    /**
     * Execute cron job to set permissions for var and pub
     *
     * @return $this
     */
    public function execute()
    {
        try {
            $baseDir = $this->directoryList->getRoot();
            
            // Define the directories we want to ensure permissions for
            $directories = [
                $baseDir . '/var',
                $baseDir . '/pub',
                $baseDir . '/generated'
            ];

            foreach ($directories as $dir) {
                if (is_dir($dir)) {
                    // Set 777 (or 775) permissions recursively. 
                    // Using shell_exec for recursive permission changes as it's faster.
                    $command = "chmod -R 777 " . escapeshellarg($dir);
                    shell_exec($command);
                    $this->logger->info("Permission cron: Set 777 permissions for {$dir}");
                }
            }
            
            // Also setting correct permissions for the magento executable 
            if (is_file($baseDir . '/bin/magento')) {
                shell_exec("chmod +x " . escapeshellarg($baseDir . '/bin/magento'));
            }

        } catch (\Exception $e) {
            $this->logger->error("Permission cron error: " . $e->getMessage());
        }

        return $this;
    }
}

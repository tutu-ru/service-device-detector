<?php
namespace RMS\DeviceDetector;
use DeviceDetector\Cache\Cache;

/**
 * Class FileCache
 *
 * Кеш для промежуточного сохранения распарсенных YML файлов библиотеки Piwik devicedetector
 * Использует как статический кеш в памяти процесса, так и файловый кеш во временной папке проекта
 *
 */


class FileCache implements Cache
{
	/**
	 * @var array Статический кеш
	 */
	protected static $_cache = [];

	/**
	 * @var string Папка для сохранения файлового кеша
	 */
	private $_cacheRootDir;

	/**
	 * Имя уникальной папки во временной папке проекта для хранения файлов
	 */
	const DD_CACHE_DIR = 'dd-cache';


	public function __construct()
	{
		$this->_cacheRootDir = fConfig()->getTempDir() . "/" . self::DD_CACHE_DIR;
		fTools()->file()->prepareDir($this->_cacheRootDir);
	}

	public function contains($id)
	{
		return $this->_containsInStatic($id) || $this->_containsInFS($id);
	}

	public function fetch($id)
	{
		if ($this->_containsInStatic($id))
			return self::$_cache[$id];

		if ($this->_containsInFS($id))
		{
			$file = $this->_id2file($id);
			$resource = fopen($file, "r");
			if (flock($resource, LOCK_SH))
			{
				$result = require($file);
				$this->_saveToStatic($id, $result);
			}
			else
			{
				$result = false;
			}
			flock($resource, LOCK_UN);
			fclose($resource);
			return $result;
		}

		return false;
	}

	public function save($id, $data, $lifeTime = null)
	{
		$this->_saveToStatic($id, $data);
		$this->_saveToFS($id, $data);
	}

	public function delete($id)
	{
		unset(self::$_cache[$id]);
		if ($this->_containsInFS($id))
			unlink($this->_id2file($id));
	}

	public function flushAll()
	{
		self::$_cache = [];
		fTools()->file()->clearDir($this->_cacheRootDir);
	}

	/**
	 * Преобразование ID элемента в кеше в конкретное имя файла
	 *
	 * @param $id
	 * @return string
	 */
	private function _id2file($id)
	{
		return $this->_cacheRootDir . "/" . $id;
	}

	private function _containsInStatic($id)
	{
		return isset(self::$_cache[$id]) || array_key_exists($id, self::$_cache);
	}

	private function _containsInFS($id)
	{
		return file_exists($this->_id2file($id));
	}

	private function _saveToStatic($id, $data)
	{
		self::$_cache[$id] = $data;
	}

	private function _saveToFS($id, $data)
	{
		file_put_contents($this->_id2file($id), '<?php return '.var_export($data, true).';', LOCK_EX);
	}

}

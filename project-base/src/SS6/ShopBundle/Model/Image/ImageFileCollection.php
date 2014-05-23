<?php

namespace SS6\ShopBundle\Model\Image;

class ImageFileCollection {

	/**
	 * @var string
	 */
	private $category;

	/**
	 * @param string $category
	 */
	public function __construct($category) {
		$this->category = $category;
	}

	/**
	 * @var \SS6\ShopBundle\Model\Image\ImageFile[]
	 */
	private $imageFiles;

	/**
	 * @param string $filename
	 * @param string|null $title
	 * @param string|null $type
	 */
	public function addImageFile($filename, $title = '', $type = null) {
		$this->imageFiles[$type ?: 0] = new ImageFile($this->category, $type, $filename, $title);
	}

	/**
	 *
	 * @param string|null $type
	 * @return \SS6\ShopBundle\Model\Image\ImageFile
	 * @throws \SS6\ShopBundle\Model\Image\Exception\TypeNotExistException
	 */
	public function getImageFile($type = null) {
		if (!array_key_exists($type ?: 0, $this->imageFiles)) {
			throw new \SS6\ShopBundle\Model\Image\Exception\TypeNotExistException($this->category, $type);
		}
		
		return $this->imageFiles[$type ?: 0];
	}

	/**
	 * @param string|null $type
	 * @return string
	 */
	public function getRelativeImageUrl($type) {
		$pathPrefix = $this->category . '/' . ($type ? $type . '/' : '');
		$filename = $this->getImageFile($type)->getFilename();
		return $pathPrefix . $filename;
	}

	/**
	 * @param string|null $type
	 * @return string
	 */
	public function getRelativeImageFilepath($type) {
		return $this->getRelativeImageUrl($type);
	}

	/**
	 * @return string
	 */
	public function getCategory() {
		return $this->category;
	}
}

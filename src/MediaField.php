<?php

namespace OptimistDigital\MediaField;

use Laravel\Nova\Fields\Field;
use OptimistDigital\MediaField\Models\Media;


class MediaField extends Field
{
    /**
     * The field's component.
     *
     * @var string
     */
    public $component = 'media-field';

    protected $multiple = false;

    protected $collection = null;

    protected $detailThumbnailSize = null;

    /**
     * @param $width - Width of the preview thumbnail in admin
     * @param null $height - Inherited from width when null
     * @return $this
     */
    public function compact($width = 36, $height = null) {
        $this->detailThumbnailSize = [$width, $height];
        return $this;
    }

    /**
     * Allow user to choose multiple files or images, like a gallery or list
     *
     * @return $this
     */
    public function multiple()
    {
        $this->multiple = true;

        return $this;
    }


    public function collection($collection)
    {
        $this->collection = $collection;
        return $this;
    }

    /**
     *
     * Prepare the element for JSON serialization.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'allowMultipleFiles' => $this->multiple,
            'order' => $this->multiple,
            'displayCollection' => $this->collection,
            'detailThumbnailSize' => $this->detailThumbnailSize
        ]);
    }

    public function resolveResponseValue($fieldValue)
    {
        $query = Media::whereIn('id', explode(',', $fieldValue));

        if ($this->multiple) {
            return $query->orderByRaw("FIELD(id, $fieldValue)")->get();
        }

        return $query->first();
    }


}

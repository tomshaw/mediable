<?php

namespace TomShaw\Mediable\Concerns;

use Livewire\Wireable;

final class ShowState implements Wireable
{
    public function __construct(
        public bool $showPagination = true,
        public bool $showPerPage = true,
        public bool $showOrderBy = true,
        public bool $showOrderDir = true,
        public bool $showColumnWidth = true,
        public bool $showUniqueMimeTypes = true,
        public bool $showSidebar = true,
        public bool $showSearch = true,
        public bool $showUpload = true,
        public bool $showEditor = true,
        public bool $showPreview = true,
        public bool $showImageStrip = true,
        public bool $showMetaInfo = true,
    ) {
    }

    public function isShowPagination(): bool
    {
        return $this->showPagination;
    }

    public function isShowPerPage(): bool
    {
        return $this->showPerPage;
    }

    public function isShowOrderBy(): bool
    {
        return $this->showOrderBy;
    }

    public function isShowOrderDir(): bool
    {
        return $this->showOrderDir;
    }

    public function isShowColumnWidth(): bool
    {
        return $this->showColumnWidth;
    }

    public function isShowUniqueMimeTypes(): bool
    {
        return $this->showUniqueMimeTypes;
    }

    public function isShowSidebar(): bool
    {
        return $this->showSidebar;
    }

    public function isShowSearch(): bool
    {
        return $this->showSearch;
    }

    public function isShowUpload(): bool
    {
        return $this->showUpload;
    }

    public function isShowEditor(): bool
    {
        return $this->showEditor;
    }

    public function isShowPreview(): bool
    {
        return $this->showPreview;
    }

    public function isShowImageStrip(): bool
    {
        return $this->showImageStrip;
    }

    public function isShowMetaInfo(): bool
    {
        return $this->showMetaInfo;
    }

    public function toLivewire()
    {
        return [
            'showPagination' => $this->showPagination,
            'showPerPage' => $this->showPerPage,
            'showOrderBy' => $this->showOrderBy,
            'showOrderDir' => $this->showOrderDir,
            'showColumnWidth' => $this->showColumnWidth,
            'showUniqueMimeTypes' => $this->showUniqueMimeTypes,
            'showSidebar' => $this->showSidebar,
            'showSearch' => $this->showSearch,
            'showUpload' => $this->showUpload,
            'showEditor' => $this->showEditor,
            'showPreview' => $this->showPreview,
            'showImageStrip' => $this->showImageStrip,
            'showMetaInfo' => $this->showMetaInfo,
        ];
    }

    public static function fromLivewire($value)
    {
        return new self(
            showPagination: $value['showPagination'],
            showPerPage: $value['showPerPage'],
            showOrderBy: $value['showOrderBy'],
            showOrderDir: $value['showOrderDir'],
            showColumnWidth: $value['showColumnWidth'],
            showUniqueMimeTypes: $value['showUniqueMimeTypes'],
            showSidebar: $value['showSidebar'],
            showSearch: $value['showSearch'],
            showUpload: $value['showUpload'],
            showEditor: $value['showEditor'],
            showPreview: $value['showPreview'],
            showImageStrip: $value['showImageStrip'],
            showMetaInfo: $value['showMetaInfo'],
        );
    }
}

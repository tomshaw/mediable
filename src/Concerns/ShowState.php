<?php

namespace TomShaw\Mediable\Concerns;

use Livewire\Wireable;

final class ShowState implements Wireable
{
    public function __construct(
        public private(set) bool $showPagination = true,
        public private(set) bool $showPerPage = true,
        public private(set) bool $showOrderBy = true,
        public private(set) bool $showOrderDir = true,
        public private(set) bool $showColumnWidth = true,
        public private(set) bool $showUniqueMimeTypes = true,
        public private(set) bool $showSidebar = true,
        public private(set) bool $showSearch = true,
        public private(set) bool $showUpload = true,
        public private(set) bool $showEditor = true,
        public private(set) bool $showPreview = true,
        public private(set) bool $showImageStrip = true,
        public private(set) bool $showMetaInfo = true,
        public private(set) bool $showAppStats = true,
    ) {}

    public function toggleSidebar(): self
    {
        return clone ($this, ['showSidebar' => ! $this->showSidebar]);
    }

    public function toggleMetaInfo(): self
    {
        return clone ($this, ['showMetaInfo' => ! $this->showMetaInfo]);
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

    public function isShowAppStats(): bool
    {
        return $this->showAppStats;
    }

    /**
     * @return array<string, bool>
     */
    public function toLivewire(): array
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
            'showAppStats' => $this->showAppStats,
        ];
    }

    /**
     * @param  array<string, bool>  $value
     */
    public static function fromLivewire($value): self
    {
        return new self(...$value);
    }
}

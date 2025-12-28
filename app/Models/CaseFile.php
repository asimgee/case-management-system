<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CaseFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'title',
        'description',
        'file_path',
        'file_name',
        'file_size',
        'file_type',
        'uploaded_by',
        'category',
        'tags',
        'is_public',
        'version',
        'previous_version_id',
    ];

    protected $casts = [
        'tags' => 'array',
        'is_public' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship with Case
     */
    public function case()
    {
        return $this->belongsTo(Case_Model::class, 'case_id');
    }

    /**
     * Relationship with User (uploader)
     */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Relationship with previous version
     */
    public function previousVersion()
    {
        return $this->belongsTo(CaseFile::class, 'previous_version_id');
    }

    /**
     * Relationship with next versions
     */
    public function nextVersions()
    {
        return $this->hasMany(CaseFile::class, 'previous_version_id');
    }

    /**
     * Get file size in human readable format
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->file_size;
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    /**
     * Get file icon based on file type
     */
    public function getFileIconAttribute()
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        $mime = $this->file_type;
        
        $iconMap = [
            'pdf' => 'fas fa-file-pdf',
            'doc' => 'fas fa-file-word',
            'docx' => 'fas fa-file-word',
            'xls' => 'fas fa-file-excel',
            'xlsx' => 'fas fa-file-excel',
            'ppt' => 'fas fa-file-powerpoint',
            'pptx' => 'fas fa-file-powerpoint',
            'txt' => 'fas fa-file-alt',
            'csv' => 'fas fa-file-csv',
            'jpg' => 'fas fa-file-image',
            'jpeg' => 'fas fa-file-image',
            'png' => 'fas fa-file-image',
            'gif' => 'fas fa-file-image',
            'svg' => 'fas fa-file-image',
            'zip' => 'fas fa-file-archive',
            'rar' => 'fas fa-file-archive',
            '7z' => 'fas fa-file-archive',
            'mp3' => 'fas fa-file-audio',
            'wav' => 'fas fa-file-audio',
            'mp4' => 'fas fa-file-video',
            'avi' => 'fas fa-file-video',
            'mov' => 'fas fa-file-video',
            'html' => 'fas fa-file-code',
            'php' => 'fas fa-file-code',
            'js' => 'fas fa-file-code',
            'css' => 'fas fa-file-code',
            'json' => 'fas fa-file-code',
            'xml' => 'fas fa-file-code',
        ];
        
        if (isset($iconMap[$extension])) {
            return $iconMap[$extension];
        }
        
        // Fallback to mime type
        if (str_contains($mime, 'pdf')) {
            return 'fas fa-file-pdf';
        } elseif (str_contains($mime, 'word') || str_contains($mime, 'document')) {
            return 'fas fa-file-word';
        } elseif (str_contains($mime, 'excel') || str_contains($mime, 'spreadsheet')) {
            return 'fas fa-file-excel';
        } elseif (str_contains($mime, 'powerpoint') || str_contains($mime, 'presentation')) {
            return 'fas fa-file-powerpoint';
        } elseif (str_contains($mime, 'image')) {
            return 'fas fa-file-image';
        } elseif (str_contains($mime, 'audio')) {
            return 'fas fa-file-audio';
        } elseif (str_contains($mime, 'video')) {
            return 'fas fa-file-video';
        } elseif (str_contains($mime, 'text')) {
            return 'fas fa-file-alt';
        } elseif (str_contains($mime, 'zip') || str_contains($mime, 'compressed')) {
            return 'fas fa-file-archive';
        } else {
            return 'fas fa-file';
        }
    }

    /**
     * Get file color based on extension
     */
    public function getFileColorAttribute()
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        
        $colorMap = [
            'pdf' => 'danger',
            'doc' => 'primary',
            'docx' => 'primary',
            'xls' => 'success',
            'xlsx' => 'success',
            'ppt' => 'warning',
            'pptx' => 'warning',
            'txt' => 'secondary',
            'csv' => 'info',
            'jpg' => 'info',
            'jpeg' => 'info',
            'png' => 'info',
            'gif' => 'info',
            'svg' => 'info',
            'zip' => 'dark',
            'rar' => 'dark',
            '7z' => 'dark',
            'mp3' => 'purple',
            'wav' => 'purple',
            'mp4' => 'pink',
            'avi' => 'pink',
            'mov' => 'pink',
            'html' => 'orange',
            'php' => 'orange',
            'js' => 'orange',
            'css' => 'orange',
            'json' => 'orange',
            'xml' => 'orange',
        ];
        
        return $colorMap[$extension] ?? 'secondary';
    }

    /**
     * Check if file is viewable in browser
     */
    public function getIsViewableAttribute()
    {
        $viewableTypes = [
            'application/pdf',
            'image/jpeg',
            'image/png',
            'image/gif',
            'image/svg+xml',
            'text/plain',
            'text/html',
            'text/css',
            'application/javascript',
            'application/json',
            'application/xml',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];
        
        return in_array($this->file_type, $viewableTypes);
    }

    /**
     * Get file URL for download
     */
    public function getDownloadUrlAttribute()
    {
        return route('case-files.download', $this->id);
    }

    /**
     * Get file URL for preview
     */
    public function getPreviewUrlAttribute()
    {
        if ($this->is_viewable) {
            return route('case-files.preview', $this->id);
        }
        
        return $this->download_url;
    }

    /**
     * Scope for public files
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for private files
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope for specific category
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope for specific case
     */
    public function scopeByCase($query, $caseId)
    {
        return $query->where('case_id', $caseId);
    }

    /**
     * Scope for uploaded by user
     */
    public function scopeUploadedBy($query, $userId)
    {
        return $query->where('uploaded_by', $userId);
    }

    /**
     * Scope for search
     */
    public function scopeSearch($query, $searchTerm)
    {
        return $query->where(function($q) use ($searchTerm) {
            $q->where('title', 'like', "%{$searchTerm}%")
              ->orWhere('description', 'like', "%{$searchTerm}%")
              ->orWhere('file_name', 'like', "%{$searchTerm}%")
              ->orWhere('tags', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Get all tags as array
     */
    public function getTagsArrayAttribute()
    {
        if (empty($this->tags)) {
            return [];
        }
        
        return is_array($this->tags) ? $this->tags : json_decode($this->tags, true);
    }

    /**
     * Check if file has specific tag
     */
    public function hasTag($tag)
    {
        $tags = $this->tags_array;
        return in_array($tag, $tags);
    }

    /**
     * Add tag to file
     */
    public function addTag($tag)
    {
        $tags = $this->tags_array;
        if (!in_array($tag, $tags)) {
            $tags[] = $tag;
            $this->tags = $tags;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Remove tag from file
     */
    public function removeTag($tag)
    {
        $tags = $this->tags_array;
        $index = array_search($tag, $tags);
        
        if ($index !== false) {
            unset($tags[$index]);
            $this->tags = array_values($tags);
            $this->save();
        }
        
        return $this;
    }

    /**
     * Create new version of file
     */
    public function createNewVersion($fileData)
    {
        $newFile = $this->replicate();
        $newFile->previous_version_id = $this->id;
        $newFile->version = $this->version + 1;
        $newFile->file_path = $fileData['file_path'] ?? $this->file_path;
        $newFile->file_name = $fileData['file_name'] ?? $this->file_name;
        $newFile->file_size = $fileData['file_size'] ?? $this->file_size;
        $newFile->file_type = $fileData['file_type'] ?? $this->file_type;
        $newFile->title = $fileData['title'] ?? $this->title . " (v" . ($this->version + 1) . ")";
        $newFile->description = $fileData['description'] ?? $this->description;
        $newFile->save();
        
        return $newFile;
    }

    /**
     * Get all versions of this file
     */
    public function getAllVersionsAttribute()
    {
        // Find the original version
        $original = $this;
        while ($original->previousVersion) {
            $original = $original->previousVersion;
        }
        
        // Get all versions from original
        $versions = collect([$original]);
        $current = $original;
        
        while ($current->nextVersions->isNotEmpty()) {
            $nextVersion = $current->nextVersions->first();
            $versions->push($nextVersion);
            $current = $nextVersion;
        }
        
        return $versions;
    }
}
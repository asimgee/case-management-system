<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'case_id',
        'name',
        'file_name',
        'original_name',
        'path',
        'size',
        'mime_type',
        'extension',
        'category',
        'description',
        'uploaded_by',
        'metadata',
        'version',
        'previous_version_id',
        'is_public',
        'tags',
        'access_level',
    ];

    protected $casts = [
        'size' => 'integer',
        'metadata' => 'array',
        'is_public' => 'boolean',
        'tags' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
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
        return $this->belongsTo(Document::class, 'previous_version_id');
    }

    /**
     * Relationship with next versions
     */
    public function nextVersions()
    {
        return $this->hasMany(Document::class, 'previous_version_id');
    }

    /**
     * Get file size in human readable format
     */
    public function getFormattedSizeAttribute()
    {
        $bytes = $this->size;
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
     * Get file icon based on extension or mime type
     */
    public function getFileIconAttribute()
    {
        $extension = strtolower($this->extension);
        $mime = $this->mime_type;
        
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
            'bmp' => 'fas fa-file-image',
            'webp' => 'fas fa-file-image',
            'zip' => 'fas fa-file-archive',
            'rar' => 'fas fa-file-archive',
            '7z' => 'fas fa-file-archive',
            'tar' => 'fas fa-file-archive',
            'gz' => 'fas fa-file-archive',
            'mp3' => 'fas fa-file-audio',
            'wav' => 'fas fa-file-audio',
            'ogg' => 'fas fa-file-audio',
            'mp4' => 'fas fa-file-video',
            'avi' => 'fas fa-file-video',
            'mov' => 'fas fa-file-video',
            'wmv' => 'fas fa-file-video',
            'flv' => 'fas fa-file-video',
            'html' => 'fas fa-file-code',
            'htm' => 'fas fa-file-code',
            'php' => 'fas fa-file-code',
            'js' => 'fas fa-file-code',
            'css' => 'fas fa-file-code',
            'json' => 'fas fa-file-code',
            'xml' => 'fas fa-file-code',
            'sql' => 'fas fa-database',
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
        } elseif (str_contains($mime, 'zip') || str_contains($mime, 'compressed') || str_contains($mime, 'archive')) {
            return 'fas fa-file-archive';
        } elseif (str_contains($mime, 'code') || str_contains($mime, 'script')) {
            return 'fas fa-file-code';
        } elseif (str_contains($mime, 'database')) {
            return 'fas fa-database';
        } else {
            return 'fas fa-file';
        }
    }

    /**
     * Get file color based on extension
     */
    public function getFileColorAttribute()
    {
        $extension = strtolower($this->extension);
        
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
            'bmp' => 'info',
            'webp' => 'info',
            'zip' => 'dark',
            'rar' => 'dark',
            '7z' => 'dark',
            'tar' => 'dark',
            'gz' => 'dark',
            'mp3' => 'purple',
            'wav' => 'purple',
            'ogg' => 'purple',
            'mp4' => 'pink',
            'avi' => 'pink',
            'mov' => 'pink',
            'wmv' => 'pink',
            'flv' => 'pink',
            'html' => 'orange',
            'htm' => 'orange',
            'php' => 'orange',
            'js' => 'orange',
            'css' => 'orange',
            'json' => 'orange',
            'xml' => 'orange',
            'sql' => 'teal',
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
            'image/bmp',
            'image/webp',
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
        
        return in_array($this->mime_type, $viewableTypes);
    }

    /**
     * Get file URL for download
     */
    public function getDownloadUrlAttribute()
    {
        return route('documents.download', $this->id);
    }

    /**
     * Get file URL for preview
     */
    public function getPreviewUrlAttribute()
    {
        if ($this->is_viewable) {
            return route('documents.preview', $this->id);
        }
        
        return $this->download_url;
    }

    /**
     * Get file full path
     */
    public function getFullPathAttribute()
    {
        return Storage::disk('public')->path($this->path);
    }

    /**
     * Get file URL
     */
    public function getUrlAttribute()
    {
        return Storage::disk('public')->url($this->path);
    }

    /**
     * Check if file exists in storage
     */
    public function getExistsAttribute()
    {
        return Storage::disk('public')->exists($this->path);
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
            $q->where('name', 'like', "%{$searchTerm}%")
              ->orWhere('original_name', 'like', "%{$searchTerm}%")
              ->orWhere('description', 'like', "%{$searchTerm}%")
              ->orWhere('category', 'like', "%{$searchTerm}%")
              ->orWhereHas('case', function($caseQuery) use ($searchTerm) {
                  $caseQuery->where('case_number', 'like', "%{$searchTerm}%")
                           ->orWhere('case_title', 'like', "%{$searchTerm}%");
              });
        });
    }

    /**
     * Scope for public documents
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope for private documents
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope for documents with specific access level
     */
    public function scopeWithAccessLevel($query, $level)
    {
        return $query->where('access_level', $level);
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
     * Check if document has specific tag
     */
    public function hasTag($tag)
    {
        $tags = $this->tags_array;
        return in_array($tag, $tags);
    }

    /**
     * Add tag to document
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
     * Remove tag from document
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
     * Create new version of document
     */
    public function createNewVersion($fileData)
    {
        $newDocument = $this->replicate();
        $newDocument->previous_version_id = $this->id;
        $newDocument->version = $this->version + 1;
        $newDocument->path = $fileData['path'] ?? $this->path;
        $newDocument->file_name = $fileData['file_name'] ?? $this->file_name;
        $newDocument->original_name = $fileData['original_name'] ?? $this->original_name;
        $newDocument->size = $fileData['size'] ?? $this->size;
        $newDocument->mime_type = $fileData['mime_type'] ?? $this->mime_type;
        $newDocument->extension = $fileData['extension'] ?? $this->extension;
        $newDocument->name = $fileData['name'] ?? $this->name . " (v" . ($this->version + 1) . ")";
        $newDocument->description = $fileData['description'] ?? $this->description;
        $newDocument->save();
        
        return $newDocument;
    }

    /**
     * Get all versions of this document
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

    /**
     * Get document metadata value
     */
    public function getMetadataValue($key, $default = null)
    {
        $metadata = $this->metadata ?? [];
        return $metadata[$key] ?? $default;
    }

    /**
     * Set document metadata value
     */
    public function setMetadataValue($key, $value)
    {
        $metadata = $this->metadata ?? [];
        $metadata[$key] = $value;
        $this->metadata = $metadata;
        $this->save();
        
        return $this;
    }

    /**
     * Remove document metadata value
     */
    public function removeMetadataValue($key)
    {
        $metadata = $this->metadata ?? [];
        if (isset($metadata[$key])) {
            unset($metadata[$key]);
            $this->metadata = $metadata;
            $this->save();
        }
        
        return $this;
    }

    /**
     * Get document access level label
     */
    public function getAccessLevelLabelAttribute()
    {
        $levels = [
            'public' => 'Public',
            'private' => 'Private',
            'confidential' => 'Confidential',
            'restricted' => 'Restricted',
        ];
        
        return $levels[$this->access_level] ?? ucfirst($this->access_level);
    }

    /**
     * Get document category label
     */
    public function getCategoryLabelAttribute()
    {
        $categories = [
            'pleading' => 'Pleading',
            'evidence' => 'Evidence',
            'motion' => 'Motion',
            'order' => 'Order',
            'judgment' => 'Judgment',
            'correspondence' => 'Correspondence',
            'research' => 'Research',
            'other' => 'Other',
        ];
        
        return $categories[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Check if user can access this document
     */
    public function canAccess($user)
    {
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($this->uploaded_by === $user->id) {
            return true;
        }
        
        if ($this->is_public) {
            return true;
        }
        
        if ($this->case && $this->case->user_id === $user->id) {
            return true;
        }
        
        return false;
    }
}
# Uploader Architecture Refactor Plan: Inheritance to Composition

## 1. Current State & Responsibilities
The current architecture relies on a rigid and fragile inheritance tree:
`UploadFolderCreate` -> `MimeValidate` -> `UploadBase` -> (Specific Types: `UploadImage`, `UploadVideo`, etc.)

*   **`UploadFolderCreate`:** Handles directory creation and adding basic `index.php` protection files.
*   **`MimeValidate`:** Manages sizing limits (max width, height, size) and validates MIME types against allowed mappings for various file categories (images, audio, video, PDF). It also contains success/error response formatting.
*   **`UploadBase`:** Orchestrates the upload flow, handles the `$_FILES` global, moves files, integrates with cloud storage (`StorageAdapterInterface`), and coordinates file naming.

This violates the Single Responsibility Principle and makes testing and extending individual pieces difficult.

## 2. Proposed Architecture (Composition)
The goal is to move from deep inheritance to dependency injection (composition) while maintaining the existing public API of `UploadBase` so consuming code does not break.

### Step 1: Extract a Filesystem Service
*   **Create `Maatify\Uploader\Services\LocalFilesystem`**
*   Move directory creation logic (`createUploadFolder`) and basic file operations (moving/copying) into this service.
*   This service will handle the physical writing of files and checking paths (e.g., `realpath`).

### Step 2: Extract a Validation Service
*   **Create `Maatify\Uploader\Services\FileValidator`**
*   Move MIME validation logic (`mimeValidate`, `mime2ext*` methods), size checks, and dimension checks into this service.
*   This service will be responsible for evaluating the `$_FILES` input and returning whether it is valid or throwing specific validation exceptions.

### Step 3: Refactor `UploadBase`
*   Change `UploadBase` to no longer extend `MimeValidate`.
*   Inject the new `LocalFilesystem` and `FileValidator` services into `UploadBase` (either via constructor injection or instantiated internally if not provided, to preserve backward compatibility).
*   Delegate the validation and file moving logic from `UploadBase` to these respective services.
*   Retain the specific abstract methods (`allowedExtensions`, `validateMime`) in `UploadBase` for the child classes, but have them configure the `FileValidator` rather than extending it directly.

### Step 4: Deprecate Legacy Classes
*   Mark `UploadFolderCreate` and `MimeValidate` as `@deprecated`.
*   Eventually remove them in a future major release once consumers have fully migrated.

## 3. Backward Compatibility Strategy
*   Keep all public setter methods (`setUploadFolder`, `setMaxSize`, `setFileInputName`, etc.) on `UploadBase`. Under the hood, these setters will configure the newly injected services.
*   Maintain the same array return structure (`['uploaded' => 1, 'file' => ...]`) for the `upload()` method.
*   Existing child classes (`UploadImage`, etc.) will not need to change their implementations of `allowedExtensions()` or `validateMime()`.
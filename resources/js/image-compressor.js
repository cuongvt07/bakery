/**
 * Client-side Image Compression
 * Reduces image file size to <500KB before upload
 * Target: Mobile optimization (iPhone HEIC → JPEG)
 */

window.compressImages = async function(input) {
    if (!input.files || input.files.length === 0) return;
    
    const maxSizeKB = 500;
    const maxDimension = 1920;
    const compressedFiles = [];
    
    for (let i = 0; i < input.files.length; i++) {
        const file = input.files[i];
        
        // Skip if already small
        if (file.size <= maxSizeKB * 1024) {
            compressedFiles.push(file);
            continue;
        }
        
        try {
            const compressed = await compressImage(file, maxSizeKB, maxDimension);
            compressedFiles.push(compressed);
            console.log(`Compressed: ${file.name} from ${formatBytes(file.size)} to ${formatBytes(compressed.size)}`);
        } catch (error) {
            console.error('Compression failed:', error);
            compressedFiles.push(file); // Fallback to original
        }
    }
    
    // Replace input files with compressed versions
    const dataTransfer = new DataTransfer();
    compressedFiles.forEach(file => dataTransfer.items.add(file));
    input.files = dataTransfer.files;
};

async function compressImage(file, maxSizeKB, maxDimension) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        
        reader.onerror = () => reject(new Error('Failed to read file'));
        
        reader.onload = (e) => {
            const img = new Image();
            
            img.onerror = () => reject(new Error('Failed to load image'));
            
            img.onload = () => {
                try {
                    // Calculate new dimensions
                    let { width, height } = img;
                    
                    if (width > maxDimension || height > maxDimension) {
                        const ratio = Math.min(maxDimension / width, maxDimension / height);
                        width = Math.floor(width * ratio);
                        height = Math.floor(height * ratio);
                    }
                    
                    // Create canvas
                    const canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    
                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);
                    
                    // Try different quality levels
                    compressWithQuality(canvas, file.name, maxSizeKB, 0.85, resolve);
                    
                } catch (error) {
                    reject(error);
                }
            };
            
            img.src = e.target.result;
        };
        
        reader.readAsDataURL(file);
    });
}

function compressWithQuality(canvas, fileName, maxSizeKB, quality, resolve) {
    canvas.toBlob((blob) => {
        if (!blob) {
            resolve(new File([blob], fileName, { type: 'image/jpeg' }));
            return;
        }
        
        // Check size
        if (blob.size <= maxSizeKB * 1024 || quality <= 0.1) {
            // Success or lowest quality reached
            const finalFile = new File([blob], fileName.replace(/\.[^/.]+$/, '.jpg'), { 
                type: 'image/jpeg',
                lastModified: Date.now()
            });
            resolve(finalFile);
        } else {
            // Try lower quality
            compressWithQuality(canvas, fileName, maxSizeKB, quality - 0.1, resolve);
        }
    }, 'image/jpeg', quality);
}

function formatBytes(bytes) {
    if (bytes >= 1048576) return (bytes / 1048576).toFixed(2) + ' MB';
    if (bytes >= 1024) return (bytes / 1024).toFixed(2) + ' KB';
    return bytes + ' B';
}

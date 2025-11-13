const sharp = require('sharp');
const fs = require('fs');
const path = require('path');

const imagesDir = path.join(__dirname, 'assets', 'images');
const outputDir = path.join(__dirname, 'assets', 'images', 'optimized');

// Create output directory if it doesn't exist
if (!fs.existsSync(outputDir)) {
  fs.mkdirSync(outputDir, { recursive: true });
}

// Get all image files
const imageFiles = fs.readdirSync(imagesDir).filter(file => {
  const ext = path.extname(file).toLowerCase();
  return ['.jpg', '.jpeg', '.png'].includes(ext);
});

console.log(`Found ${imageFiles.length} images to optimize...\n`);

// Process each image
imageFiles.forEach(async (file) => {
  const inputPath = path.join(imagesDir, file);
  const fileName = path.parse(file).name;
  const outputPath = path.join(outputDir, `${fileName}.webp`);
  
  try {
    const stats = fs.statSync(inputPath);
    const originalSize = (stats.size / 1024 / 1024).toFixed(2);
    
    // Convert to WebP with quality 80
    await sharp(inputPath)
      .webp({ quality: 80 })
      .toFile(outputPath);
    
    const newStats = fs.statSync(outputPath);
    const newSize = (newStats.size / 1024 / 1024).toFixed(2);
    const reduction = ((1 - newStats.size / stats.size) * 100).toFixed(1);
    
    console.log(`✓ ${file}`);
    console.log(`  ${originalSize} MB → ${newSize} MB (${reduction}% reduction)\n`);
  } catch (error) {
    console.error(`✗ Error processing ${file}:`, error.message);
  }
});

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Resizer</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0f0f1e 0%, #1a1a2e 50%, #16213e 100%);
            min-height: 100vh;
            padding: 20px;
            color: #e4e4e7;
        }

        .container {
            max-width: 950px;
            margin: 0 auto;
            background: rgba(30, 30, 46, 0.95);
            border-radius: 24px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            overflow: hidden;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }

        .header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            position: relative;
            padding: 48px 32px;
            text-align: center;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="20" height="20" patternUnits="userSpaceOnUse"><path d="M 20 0 L 0 0 0 20" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-size: 2.75rem;
            font-weight: 700;
            margin-bottom: 12px;
            letter-spacing: -0.02em;
            background: linear-gradient(to right, #ffffff, #e0e7ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header p {
            font-size: 1.125rem;
            opacity: 0.95;
            font-weight: 500;
            margin-bottom: 16px;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            padding: 8px 16px;
            border-radius: 24px;
            font-size: 0.875rem;
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .content {
            padding: 48px;
        }

        .message {
            padding: 16px 20px;
            border-radius: 12px;
            margin-bottom: 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.success {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.15), rgba(22, 163, 74, 0.1));
            color: #86efac;
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .message.success::before {
            content: '✓';
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: #22c55e;
            border-radius: 50%;
            font-weight: bold;
            color: white;
        }

        .message.error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.15), rgba(220, 38, 38, 0.1));
            color: #fca5a5;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        .message.error::before {
            content: '×';
            display: flex;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            background: #ef4444;
            border-radius: 50%;
            font-weight: bold;
            color: white;
            font-size: 1.25rem;
        }

        .message.hidden {
            display: none;
        }

        .form-group {
            margin-bottom: 28px;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            color: #e4e4e7;
            font-size: 0.9375rem;
            letter-spacing: -0.01em;
        }

        input[type="file"] {
            width: 100%;
            padding: 14px;
            background: rgba(39, 39, 58, 0.6);
            border: 2px dashed rgba(139, 92, 246, 0.3);
            border-radius: 12px;
            font-size: 15px;
            color: #e4e4e7;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        input[type="file"]::file-selector-button {
            padding: 8px 16px;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            margin-right: 12px;
            transition: transform 0.2s ease;
        }

        input[type="file"]::file-selector-button:hover {
            transform: translateY(-1px);
        }

        input[type="file"]:hover {
            border-color: rgba(139, 92, 246, 0.6);
            background: rgba(39, 39, 58, 0.8);
        }

        input[type="number"] {
            width: 100%;
            padding: 14px;
            background: rgba(39, 39, 58, 0.6);
            border: 2px solid rgba(99, 102, 241, 0.2);
            border-radius: 12px;
            font-size: 15px;
            color: #e4e4e7;
            transition: all 0.3s ease;
        }

        input[type="number"]:focus {
            outline: none;
            border-color: #8b5cf6;
            background: rgba(39, 39, 58, 0.9);
            box-shadow: 0 0 0 4px rgba(139, 92, 246, 0.1);
        }

        .dimension-inputs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .quality-group {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .quality-group input[type="range"] {
            flex: 1;
            height: 8px;
            border-radius: 4px;
            background: rgba(99, 102, 241, 0.2);
            outline: none;
            -webkit-appearance: none;
            appearance: none;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            appearance: none;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.4);
            transition: transform 0.2s ease;
        }

        input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.1);
        }

        input[type="range"]::-moz-range-thumb {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            cursor: pointer;
            border: none;
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.4);
        }

        .quality-value {
            background: rgba(99, 102, 241, 0.15);
            padding: 10px 20px;
            border-radius: 10px;
            font-weight: 700;
            color: #a5b4fc;
            min-width: 70px;
            text-align: center;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px;
            background: rgba(39, 39, 58, 0.4);
            border-radius: 12px;
            border: 1px solid rgba(99, 102, 241, 0.1);
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .checkbox-group:hover {
            background: rgba(39, 39, 58, 0.6);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .checkbox-group label {
            margin-bottom: 0;
            cursor: pointer;
        }

        input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #8b5cf6;
            flex-shrink: 0;
        }

        .upload-btn {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 50%, #d946ef 100%);
            color: white;
            padding: 16px 32px;
            border: none;
            border-radius: 12px;
            font-size: 1.0625rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            width: 100%;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 10px 25px -5px rgba(139, 92, 246, 0.3);
        }

        .upload-btn:hover:not(:disabled) {
            transform: translateY(-2px);
            box-shadow: 0 15px 35px -5px rgba(139, 92, 246, 0.5);
        }

        .upload-btn:active:not(:disabled) {
            transform: translateY(0);
        }

        .upload-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .result-section {
            margin-top: 32px;
            padding: 28px;
            background: rgba(99, 102, 241, 0.08);
            border-radius: 16px;
            text-align: center;
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .result-section.hidden {
            display: none;
        }

        .result-section h3 {
            color: #e4e4e7;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        .download-btn {
            display: inline-block;
            background: linear-gradient(135deg, #22c55e, #16a34a);
            color: white;
            padding: 14px 28px;
            text-decoration: none;
            border-radius: 10px;
            font-weight: 600;
            margin: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .download-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.4);
        }

        .download-btn.view {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        .download-btn.view:hover {
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.4);
        }

        .file-info {
            background: rgba(39, 39, 58, 0.6);
            padding: 20px;
            border-radius: 12px;
            margin: 20px 0;
            border-left: 4px solid #8b5cf6;
            color: #d4d4d8;
        }

        .file-info strong {
            color: #a5b4fc;
        }

        .specs {
            background: rgba(39, 39, 58, 0.4);
            padding: 28px;
            border-radius: 16px;
            margin-top: 32px;
            border: 1px solid rgba(99, 102, 241, 0.1);
        }

        .specs h3 {
            color: #e4e4e7;
            margin-bottom: 20px;
            font-size: 1.25rem;
            font-weight: 700;
        }

        .specs ul {
            list-style: none;
            padding: 0;
            display: grid;
            gap: 12px;
        }

        .specs li {
            padding: 12px 16px;
            color: #d4d4d8;
            background: rgba(99, 102, 241, 0.05);
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: all 0.3s ease;
        }

        .specs li:hover {
            background: rgba(99, 102, 241, 0.1);
            transform: translateX(4px);
        }

        .specs li::before {
            content: "•";
            color: #8b5cf6;
            font-size: 1.25rem;
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .dimension-inputs {
                grid-template-columns: 1fr;
            }

            .header h1 {
                font-size: 2rem;
            }

            .content {
                padding: 32px 24px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="header-content">
                <h1>Image Resizer</h1>
                <p>Fast, modern, and powerful image processing</p>
                <div class="badge">Created by Aymane Bouljam</div>
            </div>
        </div>

        <div class="content">
            <div id="message" class="message hidden">
                <span id="messageText"></span>
            </div>

            <form id="uploadForm">
                <div class="form-group">
                    <label for="image">Select Image:</label>
                    <input type="file" id="image" name="image" accept="image/*" required>
                </div>

                <div class="form-group">
                    <label>Resize Dimensions:</label>
                    <div class="dimension-inputs">
                        <div>
                            <label for="width">Width (px):</label>
                            <input type="number" id="width" name="width" min="1" max="5000" placeholder="e.g., 800">
                        </div>
                        <div>
                            <label for="height">Height (px):</label>
                            <input type="number" id="height" name="height" min="1" max="5000" placeholder="e.g., 600">
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="maintain_aspect_ratio" name="maintain_aspect_ratio" checked>
                        <label for="maintain_aspect_ratio">Maintain aspect ratio</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="quality">Image Quality:</label>
                    <div class="quality-group">
                        <input type="range" id="quality" name="quality" min="10" max="100" value="90">
                        <div class="quality-value" id="quality-value">90%</div>
                    </div>
                </div>

                <button type="submit" class="upload-btn">Resize Image</button>
            </form>

            <div id="resultSection" class="result-section hidden">
                <h3>Image Resized Successfully!</h3>

                <div class="file-info" id="fileInfo"></div>

                <a id="downloadBtn" href="#" download class="download-btn">
                    Download Resized Image
                </a>

                <a id="viewBtn" href="#" target="_blank" class="download-btn view">
                    View Image
                </a>
            </div>

            <div class="specs">
                <h3>Features</h3>
                <ul>
                    <li>High-quality image processing</li>
                    <li>Smart aspect ratio preservation</li>
                    <li>Adjustable quality control</li>
                    <li>Supports JPG, PNG, GIF, WebP</li>
                    <li>Maximum file size: 5 MB</li>
                    <li>Fast & efficient resizing</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const form = document.getElementById('uploadForm');
        const qualityInput = document.getElementById('quality');
        const qualityValue = document.getElementById('quality-value');
        const message = document.getElementById('message');
        const messageText = document.getElementById('messageText');
        const resultSection = document.getElementById('resultSection');
        const fileInfo = document.getElementById('fileInfo');
        const downloadBtn = document.getElementById('downloadBtn');
        const viewBtn = document.getElementById('viewBtn');
        const submitBtn = form.querySelector('button[type="submit"]');

        qualityInput.addEventListener('input', (e) => {
            qualityValue.textContent = e.target.value + '%';
        });

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const formData = new FormData(form);

            showMessage('Processing...', 'success');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Processing...';
            resultSection.classList.add('hidden');

            try {
                const response = await fetch('upload.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.type === 'success' || data.success) {
                    showMessage(data.message, 'success');
                    displayResult(data);
                } else {
                    showMessage(data.message, 'error');
                    resultSection.classList.add('hidden');
                }
            } catch (error) {
                console.error('Upload error:', error);
                showMessage('An error occurred: ' + error.message, 'error');
                resultSection.classList.add('hidden');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = 'Resize Image';
            }
        });

        function showMessage(text, type) {
            messageText.textContent = text;
            message.className = 'message ' + type;
        }

        function displayResult(data) {
            fileInfo.innerHTML = `
                <strong>New Dimensions:</strong> ${data.width} × ${data.height} pixels<br>
                <strong>File Size:</strong> ${data.file_size_formatted}
            `;

            downloadBtn.href = data.resized_path;
            downloadBtn.download = data.resized_path.split('/').pop();
            viewBtn.href = data.resized_path;

            resultSection.classList.remove('hidden');
        }
    </script>
</body>
</html>
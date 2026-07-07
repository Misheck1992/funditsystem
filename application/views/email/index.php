<style>
.email-container {
    max-width: 1400px;
    margin: 0 auto;
}
.email-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    overflow: hidden;
}
.email-card-header {
    background: #1e3a5f;
    color: #fff;
    padding: 1.25rem 1.5rem;
}
.email-card-header h5 {
    margin: 0;
    font-weight: 600;
    font-size: 1.1rem;
}
.email-card-body {
    padding: 1.5rem;
}

/* Tab Styles */
.email-tabs {
    display: flex;
    border-bottom: 2px solid #e5e7eb;
    margin-bottom: 1.5rem;
    gap: 0.5rem;
}
.email-tab {
    padding: 0.75rem 1.5rem;
    background: none;
    border: none;
    border-bottom: 3px solid transparent;
    color: #6b7280;
    font-weight: 600;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.email-tab:hover {
    color: #1e3a5f;
    background: #f8fafc;
}
.email-tab.active {
    color: #1e3a5f;
    border-bottom-color: #1e3a5f;
}
.email-tab i {
    font-size: 1rem;
}
.tab-content {
    display: none;
}
.tab-content.active {
    display: block;
}

/* Form Styles */
.form-section {
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.25rem;
    margin-bottom: 1.25rem;
}
.form-section-title {
    font-weight: 600;
    color: #1e3a5f;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.form-group {
    margin-bottom: 1rem;
}
.form-group label {
    display: block;
    font-weight: 600;
    color: #374151;
    font-size: 0.85rem;
    margin-bottom: 0.4rem;
}
.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 0.6rem 0.85rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.9rem;
    transition: all 0.2s;
}
.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    border-color: #3b82f6;
    outline: none;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}
.form-group textarea {
    min-height: 150px;
    resize: vertical;
}
.form-group small {
    color: #6b7280;
    font-size: 0.75rem;
}

/* Checkbox/Toggle Styles */
.form-check-custom {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}
.form-check-custom input[type="checkbox"] {
    width: 18px;
    height: 18px;
    cursor: pointer;
}

/* Button Styles */
.btn-send {
    background: #059669;
    color: #fff;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.btn-send:hover {
    background: #047857;
}
.btn-send:disabled {
    background: #9ca3af;
    cursor: not-allowed;
}
.btn-preview {
    background: #3b82f6;
    color: #fff;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.9rem;
    cursor: pointer;
    transition: all 0.2s;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.btn-preview:hover {
    background: #2563eb;
}
.btn-template {
    background: #f3f4f6;
    color: #374151;
    border: 1px solid #d1d5db;
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.8rem;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-template:hover {
    background: #e5e7eb;
}

/* Result/Status Styles */
.result-box {
    padding: 1rem;
    border-radius: 6px;
    margin-top: 1rem;
    display: none;
}
.result-box.success {
    background: #dcfce7;
    border: 1px solid #059669;
    color: #166534;
}
.result-box.error {
    background: #fee2e2;
    border: 1px solid #dc2626;
    color: #991b1b;
}
.result-box.info {
    background: #dbeafe;
    border: 1px solid #3b82f6;
    color: #1e40af;
}

/* Recipient Cards */
.recipient-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}
.recipient-card {
    background: #fff;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    cursor: pointer;
    transition: all 0.2s;
}
.recipient-card:hover {
    border-color: #3b82f6;
    background: #f8fafc;
}
.recipient-card.selected {
    border-color: #1e3a5f;
    background: #eff6ff;
}
.recipient-card input[type="radio"] {
    display: none;
}
.recipient-card-icon {
    width: 40px;
    height: 40px;
    background: #1e3a5f;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    margin-bottom: 0.75rem;
}
.recipient-card-title {
    font-weight: 600;
    color: #1e3a5f;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
}
.recipient-card-count {
    color: #6b7280;
    font-size: 0.8rem;
}

/* SMTP Status */
.smtp-status {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    border-radius: 6px;
    margin-bottom: 1.5rem;
}
.smtp-status.configured {
    background: #dcfce7;
    border: 1px solid #059669;
}
.smtp-status.not-configured {
    background: #fee2e2;
    border: 1px solid #dc2626;
}
.smtp-status-icon {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
}
.smtp-status.configured .smtp-status-icon {
    background: #059669;
}
.smtp-status.not-configured .smtp-status-icon {
    background: #dc2626;
}

/* Preview Modal */
.preview-frame {
    width: 100%;
    height: 500px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    background: #fff;
}

/* Templates Grid */
.templates-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 0.75rem;
    margin-bottom: 1rem;
}

/* Progress Bar for Bulk */
.bulk-progress {
    background: #e5e7eb;
    border-radius: 6px;
    height: 24px;
    overflow: hidden;
    margin-top: 1rem;
    display: none;
}
.bulk-progress-bar {
    background: linear-gradient(90deg, #1e3a5f, #3b82f6);
    height: 100%;
    border-radius: 6px;
    transition: width 0.3s;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
}

/* SMTP Info Box */
.smtp-info {
    background: #f0f9ff;
    border: 1px solid #0ea5e9;
    border-radius: 6px;
    padding: 1rem;
    margin-bottom: 1rem;
    font-size: 0.85rem;
}
.smtp-info strong {
    color: #0369a1;
}

/* Editor Toggle Styles */
.editor-toggle .toggle-btn {
    padding: 0.35rem 0.75rem;
    border: none;
    background: transparent;
    color: #6b7280;
    font-size: 0.8rem;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.2s;
}
.editor-toggle .toggle-btn:hover {
    background: #f3f4f6;
}
.editor-toggle .toggle-btn.active {
    background: #fff;
    color: #1e3a5f;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

/* Visual Editor Styles */
.content-editor-wrapper {
    border: 1px solid #d1d5db;
    border-radius: 6px;
    overflow: hidden;
}
.editor-toolbar {
    background: #f8fafc;
    border-bottom: 1px solid #e5e7eb;
    padding: 0.5rem;
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}
.editor-toolbar button {
    width: 32px;
    height: 32px;
    border: none;
    background: transparent;
    color: #374151;
    border-radius: 4px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
}
.editor-toolbar button:hover {
    background: #e5e7eb;
    color: #1e3a5f;
}
.toolbar-divider {
    width: 1px;
    background: #d1d5db;
    margin: 0 0.25rem;
}
.visual-editor {
    min-height: 200px;
    padding: 1rem;
    background: #fff;
    outline: none;
    font-size: 0.9rem;
    line-height: 1.6;
}
.visual-editor:empty:before {
    content: attr(placeholder);
    color: #9ca3af;
    pointer-events: none;
}
.visual-editor:focus {
    box-shadow: inset 0 0 0 2px rgba(59, 130, 246, 0.2);
}

/* File Input Styles */
.file-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 2px dashed #d1d5db;
    border-radius: 8px;
    background: #f9fafb;
    transition: all 0.2s;
    cursor: pointer;
    overflow: hidden;
}
.file-input-wrapper:hover {
    border-color: #3b82f6;
    background: #f0f9ff;
}
.file-input-wrapper input[type="file"] {
    position: absolute;
    left: -9999px;
}
.file-input-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #3b82f6;
    color: #fff;
    border-radius: 6px;
    font-weight: 500;
    font-size: 0.9rem;
}
.file-input-text {
    color: #6b7280;
    font-size: 0.9rem;
}

/* Attachment List Styles */
.attachment-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-top: 0.5rem;
}
.attachment-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: #f0f9ff;
    border: 1px solid #0ea5e9;
    border-radius: 6px;
    padding: 0.35rem 0.75rem;
    font-size: 0.8rem;
}
.attachment-item .remove-attachment {
    color: #dc2626;
    cursor: pointer;
    padding: 0.15rem;
}
.attachment-item .remove-attachment:hover {
    color: #991b1b;
}
</style>

<div class="main-content">
    <div class="page-header">
        <h2 class="header-title">Email Management</h2>
        <div class="header-sub-title">
            <nav class="breadcrumb breadcrumb-dash">
                <a href="<?php echo base_url('Admin')?>" class="breadcrumb-item"><i class="anticon anticon-home m-r-5"></i>Home</a>
                <span class="breadcrumb-item active">Email</span>
            </nav>
        </div>
    </div>

    <div class="email-container">
        <div class="email-card">
            <div class="email-card-header">
                <h5><i class="fa fa-envelope mr-2"></i>Email Center</h5>
            </div>
            <div class="email-card-body">

                <!-- SMTP Status -->
                <?php
                $smtp_configured = !empty($settings->email_host) && !empty($settings->email_user) && !empty($settings->email_pass);
                ?>
                <div class="smtp-status <?php echo $smtp_configured ? 'configured' : 'not-configured'; ?>">
                    <div class="smtp-status-icon">
                        <i class="fa <?php echo $smtp_configured ? 'fa-check' : 'fa-times'; ?>"></i>
                    </div>
                    <div>
                        <?php if($smtp_configured): ?>
                            <strong style="color: #166534;">SMTP Configured</strong>
                            <div style="font-size: 0.8rem; color: #166534;">
                                Host: <?php echo $settings->email_host; ?> | Port: <?php echo $settings->email_port; ?>
                            </div>
                        <?php else: ?>
                            <strong style="color: #991b1b;">SMTP Not Configured</strong>
                            <div style="font-size: 0.8rem; color: #991b1b;">
                                <a href="<?php echo base_url('settings/update/1'); ?>" style="color: #991b1b; text-decoration: underline;">Configure SMTP settings</a> to send emails
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="email-tabs">
                    <button class="email-tab active" data-tab="test">
                        <i class="fa fa-flask"></i> Test Email
                    </button>
                    <button class="email-tab" data-tab="bulk">
                        <i class="fa fa-paper-plane"></i> Bulk Email
                    </button>
                    <button class="email-tab" data-tab="excel">
                        <i class="fa fa-file-excel"></i> Excel Upload
                    </button>
                    <button class="email-tab" data-tab="logs">
                        <i class="fa fa-history"></i> Notification Logs
                    </button>
                </div>

                <!-- Test Email Tab -->
                <div id="tab-test" class="tab-content active">
                    <div class="row">
                        <div class="col-lg-8">
                            <form id="test-email-form">
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fa fa-user"></i> Recipient
                                    </div>
                                    <div class="form-group">
                                        <label>Email Address *</label>
                                        <input type="email" name="to" id="test_to" placeholder="recipient@example.com" required>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fa fa-edit"></i> Message Content
                                    </div>
                                    <div class="form-group">
                                        <label>Subject *</label>
                                        <input type="text" name="subject" id="test_subject" placeholder="Email subject" required>
                                    </div>
                                    <div class="form-group">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                            <label style="margin: 0;">Message Body *</label>
                                            <div class="editor-toggle" style="display: flex; gap: 0.25rem; background: #e5e7eb; padding: 0.25rem; border-radius: 6px;">
                                                <button type="button" class="toggle-btn active" onclick="switchEditorMode('test', 'content')" id="test_content_btn">
                                                    <i class="fa fa-align-left"></i> Content
                                                </button>
                                                <button type="button" class="toggle-btn" onclick="switchEditorMode('test', 'html')" id="test_html_btn">
                                                    <i class="fa fa-code"></i> HTML
                                                </button>
                                            </div>
                                        </div>
                                        <!-- Content Editor -->
                                        <div id="test_content_editor" class="content-editor-wrapper">
                                            <div class="editor-toolbar">
                                                <button type="button" onclick="formatText('test', 'bold')" title="Bold"><i class="fa fa-bold"></i></button>
                                                <button type="button" onclick="formatText('test', 'italic')" title="Italic"><i class="fa fa-italic"></i></button>
                                                <button type="button" onclick="formatText('test', 'underline')" title="Underline"><i class="fa fa-underline"></i></button>
                                                <span class="toolbar-divider"></span>
                                                <button type="button" onclick="formatText('test', 'heading')" title="Heading"><i class="fa fa-heading"></i></button>
                                                <button type="button" onclick="formatText('test', 'paragraph')" title="Paragraph"><i class="fa fa-paragraph"></i></button>
                                                <span class="toolbar-divider"></span>
                                                <button type="button" onclick="formatText('test', 'ul')" title="Bullet List"><i class="fa fa-list-ul"></i></button>
                                                <button type="button" onclick="formatText('test', 'ol')" title="Numbered List"><i class="fa fa-list-ol"></i></button>
                                                <span class="toolbar-divider"></span>
                                                <button type="button" onclick="formatText('test', 'link')" title="Link"><i class="fa fa-link"></i></button>
                                            </div>
                                            <div id="test_visual_editor" class="visual-editor" contenteditable="true" placeholder="Type your message here..."></div>
                                        </div>
                                        <!-- HTML Editor -->
                                        <textarea name="message" id="test_message" style="display: none;" placeholder="Enter HTML content here..." required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-check-custom">
                                            <input type="checkbox" name="use_template" id="test_use_template" value="1" checked>
                                            <span>Use company email template (header & footer)</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Attachments Section -->
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fa fa-paperclip"></i> Attachments (Optional)
                                    </div>
                                    <div class="form-group">
                                        <div class="file-input-wrapper">
                                            <input type="file" name="attachments[]" id="test_attachments" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip">
                                            <span class="file-input-btn"><i class="fa fa-cloud-upload-alt"></i> Choose Files</span>
                                            <span class="file-input-text" id="test_file_text">No files selected</span>
                                        </div>
                                        <small style="margin-top: 0.5rem; display: block;">Max 10MB per file. Supported: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, GIF, ZIP</small>
                                    </div>
                                    <div id="test_attachment_list" class="attachment-list"></div>
                                </div>

                                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                    <button type="submit" class="btn-send" id="test_send_btn" <?php echo !$smtp_configured ? 'disabled' : ''; ?>>
                                        <i class="fa fa-paper-plane"></i> Send Test Email
                                    </button>
                                    <button type="button" class="btn-preview" onclick="previewEmail('test')">
                                        <i class="fa fa-eye"></i> Preview
                                    </button>
                                </div>

                                <div id="test-result" class="result-box"></div>
                            </form>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-section" style="margin-bottom: 0;">
                                <div class="form-section-title">
                                    <i class="fa fa-lightbulb"></i> Quick Tips
                                </div>
                                <ul style="margin: 0; padding-left: 1.25rem; color: #6b7280; font-size: 0.85rem;">
                                    <li style="margin-bottom: 0.5rem;">Use the test tab to verify your SMTP configuration</li>
                                    <li style="margin-bottom: 0.5rem;">HTML formatting is supported in the message body</li>
                                    <li style="margin-bottom: 0.5rem;">The company template adds professional header and footer</li>
                                    <li style="margin-bottom: 0.5rem;">Always preview your email before sending</li>
                                </ul>
                            </div>

                            <div class="form-section" style="margin-top: 1rem; margin-bottom: 0;">
                                <div class="form-section-title">
                                    <i class="fa fa-code"></i> Available Variables
                                </div>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.5rem;">Use these in bulk emails:</p>
                                <code style="display: block; background: #1e3a5f; color: #fff; padding: 0.5rem; border-radius: 4px; font-size: 0.8rem;">
                                    {name} - Recipient name<br>
                                    {email} - Recipient email
                                </code>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Email Tab -->
                <div id="tab-bulk" class="tab-content">
                    <form id="bulk-email-form">
                        <div class="row">
                            <div class="col-lg-8">
                                <!-- Recipients Section -->
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fa fa-users"></i> Select Recipients
                                    </div>
                                    <div class="recipient-grid">
                                        <label class="recipient-card" data-type="all_customers">
                                            <input type="radio" name="recipient_type" value="all_customers">
                                            <div class="recipient-card-icon"><i class="fa fa-users"></i></div>
                                            <div class="recipient-card-title">All Customers</div>
                                            <div class="recipient-card-count"><?php echo number_format($customer_count['all_customers']); ?> recipients</div>
                                        </label>
                                        <label class="recipient-card" data-type="individual_customers">
                                            <input type="radio" name="recipient_type" value="individual_customers">
                                            <div class="recipient-card-icon"><i class="fa fa-user"></i></div>
                                            <div class="recipient-card-title">Individual</div>
                                            <div class="recipient-card-count"><?php echo number_format($customer_count['individual_customers']); ?> recipients</div>
                                        </label>
                                        <label class="recipient-card" data-type="corporate_customers">
                                            <input type="radio" name="recipient_type" value="corporate_customers">
                                            <div class="recipient-card-icon"><i class="fa fa-building"></i></div>
                                            <div class="recipient-card-title">Corporate</div>
                                            <div class="recipient-card-count"><?php echo number_format($customer_count['corporate_customers']); ?> recipients</div>
                                        </label>
                                        <label class="recipient-card" data-type="active_borrowers">
                                            <input type="radio" name="recipient_type" value="active_borrowers">
                                            <div class="recipient-card-icon"><i class="fa fa-hand-holding-usd"></i></div>
                                            <div class="recipient-card-title">Active Borrowers</div>
                                            <div class="recipient-card-count"><?php echo number_format($customer_count['active_borrowers']); ?> recipients</div>
                                        </label>
                                        <label class="recipient-card" data-type="arrears_customers">
                                            <input type="radio" name="recipient_type" value="arrears_customers">
                                            <div class="recipient-card-icon" style="background: #dc2626;"><i class="fa fa-exclamation-triangle"></i></div>
                                            <div class="recipient-card-title">In Arrears</div>
                                            <div class="recipient-card-count"><?php echo number_format($customer_count['arrears_customers']); ?> recipients</div>
                                        </label>
                                        <label class="recipient-card" data-type="employees">
                                            <input type="radio" name="recipient_type" value="employees">
                                            <div class="recipient-card-icon" style="background: #7c3aed;"><i class="fa fa-user-tie"></i></div>
                                            <div class="recipient-card-title">Employees</div>
                                            <div class="recipient-card-count"><?php echo number_format($customer_count['employees']); ?> recipients</div>
                                        </label>
                                        <label class="recipient-card" data-type="custom">
                                            <input type="radio" name="recipient_type" value="custom">
                                            <div class="recipient-card-icon" style="background: #0ea5e9;"><i class="fa fa-list"></i></div>
                                            <div class="recipient-card-title">Custom List</div>
                                            <div class="recipient-card-count">Enter manually</div>
                                        </label>
                                    </div>

                                    <!-- Custom Email List (hidden by default) -->
                                    <div id="custom-emails-section" style="display: none; margin-top: 1rem;">
                                        <div class="form-group">
                                            <label>Enter Email Addresses</label>
                                            <textarea name="custom_emails" id="custom_emails" rows="4" placeholder="Enter email addresses, one per line or separated by commas:
john@example.com
jane@example.com
user@domain.com"></textarea>
                                            <small>Separate multiple emails with commas, semicolons, or new lines</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Message Section -->
                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="fa fa-edit"></i> Compose Message
                                    </div>

                                    <!-- Templates -->
                                    <div style="margin-bottom: 1rem;">
                                        <label style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.5rem; display: block;">Quick Templates</label>
                                        <div class="templates-grid">
                                            <?php foreach($templates as $index => $template): ?>
                                            <button type="button" class="btn-template" onclick="loadTemplate(<?php echo $index; ?>)">
                                                <?php echo $template['name']; ?>
                                            </button>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Subject *</label>
                                        <input type="text" name="subject" id="bulk_subject" placeholder="Email subject" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Message Body (HTML supported) *</label>
                                        <textarea name="message" id="bulk_message" rows="10" placeholder="Enter your email message here...

Use {name} to personalize with recipient's name" required></textarea>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-check-custom">
                                            <input type="checkbox" name="use_template" id="bulk_use_template" value="1" checked>
                                            <span>Use company email template (header & footer)</span>
                                        </label>
                                    </div>
                                </div>

                                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                    <button type="submit" class="btn-send" id="bulk_send_btn" <?php echo !$smtp_configured ? 'disabled' : ''; ?>>
                                        <i class="fa fa-paper-plane"></i> Send Bulk Email
                                    </button>
                                    <button type="button" class="btn-preview" onclick="previewEmail('bulk')">
                                        <i class="fa fa-eye"></i> Preview
                                    </button>
                                </div>

                                <div class="bulk-progress" id="bulk-progress">
                                    <div class="bulk-progress-bar" id="bulk-progress-bar" style="width: 0%;">0%</div>
                                </div>

                                <div id="bulk-result" class="result-box"></div>
                            </div>

                            <div class="col-lg-4">
                                <div class="smtp-info">
                                    <strong><i class="fa fa-info-circle"></i> Important Notes:</strong>
                                    <ul style="margin: 0.5rem 0 0 1rem; padding: 0; font-size: 0.8rem;">
                                        <li>Bulk emails are sent one at a time with a small delay</li>
                                        <li>Large lists may take several minutes to complete</li>
                                        <li>Use {name} variable to personalize messages</li>
                                        <li>Always test with a small group first</li>
                                    </ul>
                                </div>

                                <div class="form-section" style="margin-bottom: 0;">
                                    <div class="form-section-title">
                                        <i class="fa fa-chart-bar"></i> Recipient Summary
                                    </div>
                                    <table style="width: 100%; font-size: 0.85rem;">
                                        <tr>
                                            <td style="padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">All Customers</td>
                                            <td style="padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 600;"><?php echo number_format($customer_count['all_customers']); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">Individual</td>
                                            <td style="padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 600;"><?php echo number_format($customer_count['individual_customers']); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">Corporate</td>
                                            <td style="padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 600;"><?php echo number_format($customer_count['corporate_customers']); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">Active Borrowers</td>
                                            <td style="padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 600;"><?php echo number_format($customer_count['active_borrowers']); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb;">In Arrears</td>
                                            <td style="padding: 0.5rem 0; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: 600; color: #dc2626;"><?php echo number_format($customer_count['arrears_customers']); ?></td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 0.5rem 0;">Employees</td>
                                            <td style="padding: 0.5rem 0; text-align: right; font-weight: 600;"><?php echo number_format($customer_count['employees']); ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Excel Upload Tab -->
                <div id="tab-excel" class="tab-content">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Step 1: Upload Excel -->
                            <div class="form-section">
                                <div class="form-section-title">
                                    <i class="fa fa-upload"></i> Step 1: Upload Excel File
                                </div>
                                <div style="background: #f0f9ff; border: 1px solid #0ea5e9; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                                    <p style="margin: 0; color: #0369a1; font-size: 0.85rem;">
                                        <i class="fa fa-info-circle mr-1"></i>
                                        Upload an Excel file with columns for <strong>Name</strong> and <strong>Email</strong>.
                                        The system will automatically detect the columns.
                                    </p>
                                </div>
                                <div class="form-group">
                                    <label>Select Excel File (XLS, XLSX, or CSV)</label>
                                    <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                                        <div style="flex: 1; min-width: 250px;">
                                            <div style="position: relative; border: 2px dashed #d1d5db; border-radius: 8px; padding: 1.5rem; text-align: center; background: #f9fafb; cursor: pointer; transition: all 0.2s;"
                                                 onclick="document.getElementById('excel_file').click();"
                                                 id="excel_drop_zone">
                                                <input type="file" name="excel_file" id="excel_file" accept=".xls,.xlsx,.csv"
                                                       style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer;">
                                                <div id="excel_file_label">
                                                    <i class="fa fa-file-excel" style="font-size: 2rem; color: #059669; margin-bottom: 0.5rem; display: block;"></i>
                                                    <span style="color: #374151; font-weight: 500;">Click to select file or drag & drop</span>
                                                    <br><small style="color: #6b7280;">XLS, XLSX, or CSV</small>
                                                </div>
                                                <div id="excel_file_selected" style="display: none;">
                                                    <i class="fa fa-check-circle" style="font-size: 2rem; color: #059669; margin-bottom: 0.5rem; display: block;"></i>
                                                    <span style="color: #059669; font-weight: 600;" id="excel_file_name">filename.xlsx</span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn-send" onclick="uploadExcelFile()" id="upload_excel_btn" style="padding: 1rem 1.5rem;">
                                            <i class="fa fa-upload"></i> Upload & Process
                                        </button>
                                    </div>
                                    <small style="display: block; margin-top: 0.75rem;">
                                        <a href="<?php echo base_url('email/download_sample'); ?>" style="color: #3b82f6; text-decoration: none;">
                                            <i class="fa fa-download"></i> Download sample template (CSV)
                                        </a>
                                        <span style="color: #6b7280; margin-left: 0.5rem;">- Recommended format</span>
                                    </small>
                                </div>

                                <!-- Upload Result -->
                                <div id="excel-upload-result" style="display: none;"></div>

                                <!-- Recipients Preview -->
                                <div id="excel-recipients-preview" style="display: none; margin-top: 1rem;">
                                    <div style="background: #dcfce7; border: 1px solid #059669; border-radius: 8px; padding: 1rem;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                                            <strong style="color: #166534;">
                                                <i class="fa fa-check-circle mr-1"></i>
                                                <span id="excel-recipient-count">0</span> Recipients Found
                                            </strong>
                                            <button type="button" onclick="clearExcelUpload()" style="background: none; border: none; color: #dc2626; cursor: pointer; font-size: 0.85rem;">
                                                <i class="fa fa-times"></i> Clear
                                            </button>
                                        </div>
                                        <table style="width: 100%; font-size: 0.85rem; background: #fff; border-radius: 6px;">
                                            <thead>
                                                <tr style="background: #f0fdf4;">
                                                    <th style="padding: 0.5rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Name</th>
                                                    <th style="padding: 0.5rem; text-align: left; border-bottom: 1px solid #e5e7eb;">Email</th>
                                                </tr>
                                            </thead>
                                            <tbody id="excel-preview-table">
                                            </tbody>
                                        </table>
                                        <p id="excel-more-text" style="margin: 0.5rem 0 0 0; font-size: 0.8rem; color: #166534; display: none;"></p>
                                    </div>
                                </div>
                            </div>

                            <!-- Step 2: Compose Message -->
                            <div class="form-section" id="excel-compose-section" style="opacity: 0.5; pointer-events: none;">
                                <div class="form-section-title">
                                    <i class="fa fa-edit"></i> Step 2: Compose Message
                                </div>

                                <!-- Name Personalization Option -->
                                <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 1rem; margin-bottom: 1rem;">
                                    <label class="form-check-custom" style="margin-bottom: 0;">
                                        <input type="checkbox" name="use_name" id="excel_use_name" value="1" checked>
                                        <span style="color: #92400e;">
                                            <strong>Personalize with recipient name</strong> - Use <code style="background: #1e3a5f; color: #fff; padding: 2px 6px; border-radius: 3px;">{name}</code> in your message to automatically insert each recipient's name
                                        </span>
                                    </label>
                                </div>

                                <!-- Templates -->
                                <div style="margin-bottom: 1rem;">
                                    <label style="font-weight: 600; color: #374151; font-size: 0.85rem; margin-bottom: 0.5rem; display: block;">Quick Templates</label>
                                    <div class="templates-grid">
                                        <?php foreach($templates as $index => $template): ?>
                                        <button type="button" class="btn-template" onclick="loadExcelTemplate(<?php echo $index; ?>)">
                                            <?php echo $template['name']; ?>
                                        </button>
                                        <?php endforeach; ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Subject *</label>
                                    <input type="text" name="subject" id="excel_subject" placeholder="Email subject" required>
                                </div>
                                <div class="form-group">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                        <label style="margin: 0;">Message Body *</label>
                                        <div class="editor-toggle" style="display: flex; gap: 0.25rem; background: #e5e7eb; padding: 0.25rem; border-radius: 6px;">
                                            <button type="button" class="toggle-btn active" onclick="switchEditorMode('excel', 'content')" id="excel_content_btn">
                                                <i class="fa fa-align-left"></i> Content
                                            </button>
                                            <button type="button" class="toggle-btn" onclick="switchEditorMode('excel', 'html')" id="excel_html_btn">
                                                <i class="fa fa-code"></i> HTML
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Content Editor -->
                                    <div id="excel_content_editor" class="content-editor-wrapper">
                                        <div class="editor-toolbar">
                                            <button type="button" onclick="formatText('excel', 'bold')" title="Bold"><i class="fa fa-bold"></i></button>
                                            <button type="button" onclick="formatText('excel', 'italic')" title="Italic"><i class="fa fa-italic"></i></button>
                                            <button type="button" onclick="formatText('excel', 'underline')" title="Underline"><i class="fa fa-underline"></i></button>
                                            <span class="toolbar-divider"></span>
                                            <button type="button" onclick="formatText('excel', 'heading')" title="Heading"><i class="fa fa-heading"></i></button>
                                            <button type="button" onclick="formatText('excel', 'paragraph')" title="Paragraph"><i class="fa fa-paragraph"></i></button>
                                            <span class="toolbar-divider"></span>
                                            <button type="button" onclick="formatText('excel', 'ul')" title="Bullet List"><i class="fa fa-list-ul"></i></button>
                                            <button type="button" onclick="formatText('excel', 'ol')" title="Numbered List"><i class="fa fa-list-ol"></i></button>
                                            <span class="toolbar-divider"></span>
                                            <button type="button" onclick="formatText('excel', 'link')" title="Link"><i class="fa fa-link"></i></button>
                                            <button type="button" onclick="insertVariable('excel', '{name}')" title="Insert Name Variable" style="width: auto; padding: 0 0.5rem; font-size: 0.75rem; background: #fef3c7; color: #92400e;">{name}</button>
                                        </div>
                                        <div id="excel_visual_editor" class="visual-editor" contenteditable="true" placeholder="Dear {name}, type your message here..."></div>
                                    </div>
                                    <!-- HTML Editor -->
                                    <textarea name="message" id="excel_message" style="display: none;" rows="10" placeholder="Enter HTML content here..." required></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-check-custom">
                                        <input type="checkbox" name="use_template" id="excel_use_template" value="1" checked>
                                        <span>Use company email template (header & footer)</span>
                                    </label>
                                </div>

                                <!-- Attachments Section -->
                                <div class="form-group" style="margin-top: 1rem;">
                                    <label><i class="fa fa-paperclip"></i> Attachments (Optional)</label>
                                    <div class="file-input-wrapper">
                                        <input type="file" name="excel_attachments[]" id="excel_attachments" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif,.zip">
                                        <span class="file-input-btn"><i class="fa fa-cloud-upload-alt"></i> Choose Files</span>
                                        <span class="file-input-text" id="excel_file_text">No files selected</span>
                                    </div>
                                    <small style="margin-top: 0.5rem; display: block;">Same attachments will be sent to all recipients. Max 10MB per file.</small>
                                    <div id="excel_attachment_list" class="attachment-list"></div>
                                </div>

                                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                                    <button type="button" class="btn-send" id="excel_send_btn" onclick="sendExcelBulk()" <?php echo !$smtp_configured ? 'disabled' : ''; ?>>
                                        <i class="fa fa-paper-plane"></i> Send to All Recipients
                                    </button>
                                    <button type="button" class="btn-preview" onclick="previewEmail('excel')">
                                        <i class="fa fa-eye"></i> Preview
                                    </button>
                                </div>

                                <div class="bulk-progress" id="excel-progress">
                                    <div class="bulk-progress-bar" id="excel-progress-bar" style="width: 0%;">0%</div>
                                </div>

                                <div id="excel-result" class="result-box"></div>
                            </div>
                        </div>

                        <div class="col-lg-4">
                            <div class="form-section" style="margin-bottom: 1rem;">
                                <div class="form-section-title">
                                    <i class="fa fa-file-alt"></i> Excel File Requirements
                                </div>
                                <ul style="margin: 0; padding-left: 1.25rem; color: #6b7280; font-size: 0.85rem;">
                                    <li style="margin-bottom: 0.5rem;">First row should be the header row</li>
                                    <li style="margin-bottom: 0.5rem;">Must have an <strong>Email</strong> column</li>
                                    <li style="margin-bottom: 0.5rem;"><strong>Name</strong> column is optional but recommended</li>
                                    <li style="margin-bottom: 0.5rem;">Supported formats: XLS, XLSX, CSV</li>
                                    <li style="margin-bottom: 0.5rem;">Duplicate emails will be removed automatically</li>
                                </ul>
                            </div>

                            <div class="form-section" style="margin-bottom: 1rem;">
                                <div class="form-section-title">
                                    <i class="fa fa-table"></i> Expected Column Names
                                </div>
                                <p style="color: #6b7280; font-size: 0.85rem; margin-bottom: 0.5rem;">The system looks for these column headers:</p>
                                <div style="background: #f8fafc; border-radius: 6px; padding: 0.75rem;">
                                    <p style="margin: 0 0 0.5rem 0; font-size: 0.8rem;">
                                        <strong style="color: #1e3a5f;">Name column:</strong><br>
                                        <code>Name</code>, <code>Full Name</code>, <code>Firstname</code>, <code>Customer Name</code>
                                    </p>
                                    <p style="margin: 0; font-size: 0.8rem;">
                                        <strong style="color: #1e3a5f;">Email column:</strong><br>
                                        <code>Email</code>, <code>Email Address</code>, <code>E-mail</code>, <code>Mail</code>
                                    </p>
                                </div>
                            </div>

                            <div class="form-section" style="margin-bottom: 0;">
                                <div class="form-section-title">
                                    <i class="fa fa-code"></i> Available Variables
                                </div>
                                <code style="display: block; background: #1e3a5f; color: #fff; padding: 0.75rem; border-radius: 4px; font-size: 0.8rem;">
                                    {name} - Recipient's name from Excel<br>
                                    {email} - Recipient's email address
                                </code>
                                <p style="margin: 0.75rem 0 0 0; color: #6b7280; font-size: 0.8rem;">
                                    <strong>Example:</strong><br>
                                    "Dear {name}, we are writing to inform you..."
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notification Logs Tab -->
                <div id="tab-logs" class="tab-content">
                    <div class="form-section" style="margin-bottom: 1rem;">
                        <div class="form-section-title">
                            <i class="fa fa-filter"></i> Filter Logs
                        </div>
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Type</label>
                                    <select id="log_filter_type" class="form-control">
                                        <option value="">All Types</option>
                                        <option value="loan_created">Loan Created</option>
                                        <option value="loan_recommended">Loan Recommended</option>
                                        <option value="test">Test</option>
                                        <option value="bulk">Bulk</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select id="log_filter_status" class="form-control">
                                        <option value="">All Statuses</option>
                                        <option value="sent">Sent</option>
                                        <option value="failed">Failed</option>
                                        <option value="pending">Pending</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="text" id="log_filter_email" class="form-control" placeholder="Search email...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>From Date</label>
                                    <input type="date" id="log_filter_from" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>To Date</label>
                                    <input type="date" id="log_filter_to" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="button" class="btn-send" style="width: 100%; padding: 0.6rem;" onclick="loadNotificationLogs()">
                                        <i class="fa fa-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table id="notification-logs-table" class="table table-striped" style="width: 100%;">
                            <thead style="background: #1e3a5f; color: #fff;">
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Reference</th>
                                    <th>Recipient</th>
                                    <th>Subject</th>
                                    <th>Status</th>
                                    <th>Triggered By</th>
                                    <th>Date</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>

                    <!-- Stats Summary -->
                    <div class="row" style="margin-top: 1.5rem;">
                        <div class="col-md-3">
                            <div style="background: #dcfce7; border-radius: 8px; padding: 1rem; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #166534;" id="stats_sent">0</div>
                                <div style="font-size: 0.85rem; color: #166534;">Emails Sent</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div style="background: #fee2e2; border-radius: 8px; padding: 1rem; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #991b1b;" id="stats_failed">0</div>
                                <div style="font-size: 0.85rem; color: #991b1b;">Failed</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div style="background: #dbeafe; border-radius: 8px; padding: 1rem; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #1e40af;" id="stats_loan_created">0</div>
                                <div style="font-size: 0.85rem; color: #1e40af;">Loan Created Alerts</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div style="background: #fef3c7; border-radius: 8px; padding: 1rem; text-align: center;">
                                <div style="font-size: 1.5rem; font-weight: bold; color: #92400e;" id="stats_loan_recommended">0</div>
                                <div style="font-size: 0.85rem; color: #92400e;">Loan Recommended Alerts</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="preview_modal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content" style="border: none; border-radius: 12px; overflow: hidden;">
            <div class="modal-header" style="background: #1e3a5f; border: none; padding: 1rem 1.5rem;">
                <h5 class="modal-title" style="font-weight: 600; color: #fff;">
                    <i class="fa fa-eye mr-2"></i>Email Preview
                </h5>
                <button type="button" class="close" data-dismiss="modal" style="color: #fff; opacity: 0.8;">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 0;">
                <iframe id="preview_frame" class="preview-frame"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
// Email templates data
var emailTemplates = <?php echo json_encode($templates); ?>;

// Tab switching
document.querySelectorAll('.email-tab').forEach(function(tab) {
    tab.addEventListener('click', function() {
        var tabId = this.getAttribute('data-tab');

        // Update tab buttons
        document.querySelectorAll('.email-tab').forEach(function(t) {
            t.classList.remove('active');
        });
        this.classList.add('active');

        // Update tab content
        document.querySelectorAll('.tab-content').forEach(function(content) {
            content.classList.remove('active');
        });
        document.getElementById('tab-' + tabId).classList.add('active');
    });
});

// Recipient card selection
document.querySelectorAll('.recipient-card').forEach(function(card) {
    card.addEventListener('click', function() {
        document.querySelectorAll('.recipient-card').forEach(function(c) {
            c.classList.remove('selected');
        });
        this.classList.add('selected');

        var type = this.getAttribute('data-type');
        var customSection = document.getElementById('custom-emails-section');
        if (type === 'custom') {
            customSection.style.display = 'block';
        } else {
            customSection.style.display = 'none';
        }
    });
});

// Load template
function loadTemplate(index) {
    var template = emailTemplates[index];
    document.getElementById('bulk_subject').value = template.subject;
    document.getElementById('bulk_message').value = template.body;
}

// Preview email
function previewEmail(type) {
    var subject, message, useTemplate;

    if (type === 'test') {
        subject = document.getElementById('test_subject').value;
        message = document.getElementById('test_message').value;
        useTemplate = document.getElementById('test_use_template').checked ? '1' : '0';
    } else if (type === 'excel') {
        subject = document.getElementById('excel_subject').value;
        message = document.getElementById('excel_message').value;
        useTemplate = document.getElementById('excel_use_template').checked ? '1' : '0';
        // Replace {name} with sample name for preview
        message = message.replace(/{name}/gi, 'John Doe').replace(/{email}/gi, 'john.doe@example.com');
    } else {
        subject = document.getElementById('bulk_subject').value;
        message = document.getElementById('bulk_message').value;
        useTemplate = document.getElementById('bulk_use_template').checked ? '1' : '0';
    }

    if (!subject || !message) {
        alert('Please enter subject and message to preview');
        return;
    }

    $.ajax({
        url: '<?php echo base_url("email/preview"); ?>',
        type: 'POST',
        data: {
            subject: subject,
            message: message,
            use_template: useTemplate
        },
        success: function(response) {
            var iframe = document.getElementById('preview_frame');
            iframe.srcdoc = response;
            $('#preview_modal').modal('show');
        }
    });
}

// Test email form submit
document.getElementById('test-email-form').addEventListener('submit', function(e) {
    e.preventDefault();

    // Sync visual editor content before sending
    syncEditorContent('test');

    var btn = document.getElementById('test_send_btn');
    var result = document.getElementById('test-result');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
    result.style.display = 'none';

    // Use FormData to support file attachments
    var formData = new FormData();
    formData.append('to', document.getElementById('test_to').value);
    formData.append('subject', document.getElementById('test_subject').value);
    formData.append('message', document.getElementById('test_message').value);
    formData.append('use_template', document.getElementById('test_use_template').checked ? '1' : '0');

    // Add attachments
    var attachments = document.getElementById('test_attachments').files;
    for (var i = 0; i < attachments.length; i++) {
        formData.append('attachments[]', attachments[i]);
    }

    $.ajax({
        url: '<?php echo base_url("email/send_test"); ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send Test Email';

            result.style.display = 'block';
            if (response.success) {
                result.className = 'result-box success';
                var msg = '<i class="fa fa-check-circle"></i> ' + response.message;
                if (response.attachments_count) {
                    msg += ' (' + response.attachments_count + ' attachment(s))';
                }
                result.innerHTML = msg;
            } else {
                result.className = 'result-box error';
                result.innerHTML = '<i class="fa fa-times-circle"></i> ' + response.message;
                if (response.debug) {
                    console.log('Email debug:', response.debug);
                }
            }
        },
        error: function(xhr, status, error) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send Test Email';
            result.style.display = 'block';
            result.className = 'result-box error';
            result.innerHTML = '<i class="fa fa-times-circle"></i> An error occurred while sending';
            console.log('AJAX Error:', xhr.responseText);
        }
    });
});

// Bulk email form submit
document.getElementById('bulk-email-form').addEventListener('submit', function(e) {
    e.preventDefault();

    var recipientType = document.querySelector('input[name="recipient_type"]:checked');
    if (!recipientType) {
        alert('Please select a recipient group');
        return;
    }

    var count = 0;
    var selectedCard = document.querySelector('.recipient-card.selected');
    if (selectedCard) {
        var countText = selectedCard.querySelector('.recipient-card-count').textContent;
        count = parseInt(countText.replace(/[^0-9]/g, '')) || 0;
    }

    if (recipientType.value !== 'custom' && count === 0) {
        alert('The selected group has no recipients with email addresses');
        return;
    }

    var confirmMsg = 'Are you sure you want to send this email to ';
    if (recipientType.value === 'custom') {
        confirmMsg += 'the custom list?';
    } else {
        confirmMsg += count + ' recipients?';
    }

    if (!confirm(confirmMsg)) {
        return;
    }

    var btn = document.getElementById('bulk_send_btn');
    var result = document.getElementById('bulk-result');
    var progress = document.getElementById('bulk-progress');
    var progressBar = document.getElementById('bulk-progress-bar');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
    result.style.display = 'none';
    progress.style.display = 'block';
    progressBar.style.width = '0%';
    progressBar.textContent = 'Preparing...';

    // Simulate progress while sending
    var progressInterval = setInterval(function() {
        var current = parseInt(progressBar.style.width) || 0;
        if (current < 90) {
            current += Math.random() * 5;
            progressBar.style.width = current + '%';
            progressBar.textContent = Math.round(current) + '%';
        }
    }, 500);

    $.ajax({
        url: '<?php echo base_url("email/send_bulk"); ?>',
        type: 'POST',
        data: {
            recipient_type: recipientType.value,
            custom_emails: document.getElementById('custom_emails').value,
            subject: document.getElementById('bulk_subject').value,
            message: document.getElementById('bulk_message').value,
            use_template: document.getElementById('bulk_use_template').checked ? '1' : '0'
        },
        dataType: 'json',
        success: function(response) {
            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';

            setTimeout(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send Bulk Email';
                progress.style.display = 'none';

                result.style.display = 'block';
                if (response.success) {
                    var html = '<i class="fa fa-check-circle"></i> ' + response.message;
                    if (response.errors && response.errors.length > 0) {
                        html += '<br><br><strong>Failed emails:</strong><ul style="margin: 0.5rem 0 0 1rem;">';
                        response.errors.forEach(function(err) {
                            html += '<li style="font-size: 0.85rem;">' + err + '</li>';
                        });
                        html += '</ul>';
                    }
                    result.className = 'result-box success';
                    result.innerHTML = html;
                } else {
                    result.className = 'result-box error';
                    result.innerHTML = '<i class="fa fa-times-circle"></i> ' + response.message;
                }
            }, 500);
        },
        error: function() {
            clearInterval(progressInterval);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send Bulk Email';
            progress.style.display = 'none';
            result.style.display = 'block';
            result.className = 'result-box error';
            result.innerHTML = '<i class="fa fa-times-circle"></i> An error occurred while sending';
        }
    });
});

// Notification Logs DataTable
var logsTable = null;

function loadNotificationLogs() {
    var params = {
        type: document.getElementById('log_filter_type').value,
        status: document.getElementById('log_filter_status').value,
        email: document.getElementById('log_filter_email').value,
        from: document.getElementById('log_filter_from').value,
        to: document.getElementById('log_filter_to').value
    };

    var queryString = Object.keys(params)
        .filter(function(key) { return params[key]; })
        .map(function(key) { return key + '=' + encodeURIComponent(params[key]); })
        .join('&');

    if (logsTable) {
        logsTable.destroy();
    }

    logsTable = $('#notification-logs-table').DataTable({
        ajax: {
            url: '<?php echo base_url("email/notification_logs"); ?>' + (queryString ? '?' + queryString : ''),
            dataSrc: function(json) {
                // Update stats
                var sent = 0, failed = 0, loanCreated = 0, loanRecommended = 0;
                json.data.forEach(function(row) {
                    if (row.status.indexOf('Sent') > -1) sent++;
                    if (row.status.indexOf('Failed') > -1) failed++;
                    if (row.type.indexOf('Loan Created') > -1) loanCreated++;
                    if (row.type.indexOf('Loan Recommended') > -1) loanRecommended++;
                });
                document.getElementById('stats_sent').textContent = sent;
                document.getElementById('stats_failed').textContent = failed;
                document.getElementById('stats_loan_created').textContent = loanCreated;
                document.getElementById('stats_loan_recommended').textContent = loanRecommended;
                return json.data;
            }
        },
        columns: [
            { data: 'log_id' },
            { data: 'type' },
            { data: 'reference' },
            { data: 'recipient' },
            { data: 'subject' },
            { data: 'status' },
            { data: 'triggered_by' },
            { data: 'created_at' },
            { data: 'error' }
        ],
        order: [[0, 'desc']],
        pageLength: 25,
        responsive: true,
        language: {
            emptyTable: 'No notification logs found',
            loadingRecords: '<i class="fa fa-spinner fa-spin"></i> Loading...'
        },
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fa fa-file-excel"></i> Excel',
                className: 'btn btn-sm btn-success',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] }
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf"></i> PDF',
                className: 'btn btn-sm btn-danger',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-sm btn-info',
                exportOptions: { columns: [0, 1, 2, 3, 4, 5, 6, 7] }
            }
        ]
    });
}

// Load logs when tab is clicked
document.querySelector('.email-tab[data-tab="logs"]').addEventListener('click', function() {
    setTimeout(function() {
        if (!logsTable) {
            loadNotificationLogs();
        }
    }, 100);
});

// ============================================
// Excel Upload Functions
// ============================================

var excelRecipientsLoaded = false;

// File input change handler
document.getElementById('excel_file').addEventListener('change', function() {
    var fileLabel = document.getElementById('excel_file_label');
    var fileSelected = document.getElementById('excel_file_selected');
    var fileName = document.getElementById('excel_file_name');
    var dropZone = document.getElementById('excel_drop_zone');

    if (this.files && this.files[0]) {
        fileLabel.style.display = 'none';
        fileSelected.style.display = 'block';
        fileName.textContent = this.files[0].name;
        dropZone.style.borderColor = '#059669';
        dropZone.style.background = '#f0fdf4';
    } else {
        fileLabel.style.display = 'block';
        fileSelected.style.display = 'none';
        dropZone.style.borderColor = '#d1d5db';
        dropZone.style.background = '#f9fafb';
    }
});

// Drag and drop handlers
var dropZone = document.getElementById('excel_drop_zone');
if (dropZone) {
    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.borderColor = '#3b82f6';
        this.style.background = '#eff6ff';
    });

    dropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        if (!document.getElementById('excel_file').files.length) {
            this.style.borderColor = '#d1d5db';
            this.style.background = '#f9fafb';
        } else {
            this.style.borderColor = '#059669';
            this.style.background = '#f0fdf4';
        }
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        var fileInput = document.getElementById('excel_file');
        if (e.dataTransfer.files.length) {
            fileInput.files = e.dataTransfer.files;
            // Trigger change event
            var event = new Event('change', { bubbles: true });
            fileInput.dispatchEvent(event);
        }
    });
}

function uploadExcelFile() {
    var fileInput = document.getElementById('excel_file');
    var file = fileInput.files[0];

    if (!file) {
        alert('Please select an Excel file to upload');
        return;
    }

    var btn = document.getElementById('upload_excel_btn');
    var resultDiv = document.getElementById('excel-upload-result');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Uploading...';
    resultDiv.style.display = 'none';

    var formData = new FormData();
    formData.append('excel_file', file);

    $.ajax({
        url: '<?php echo base_url("email/upload_excel"); ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-upload"></i> Upload';

            if (response.success) {
                resultDiv.style.display = 'none';
                showExcelPreview(response.count, response.preview);
                enableExcelCompose();
                excelRecipientsLoaded = true;
            } else {
                resultDiv.style.display = 'block';
                resultDiv.className = 'result-box error';
                resultDiv.innerHTML = '<i class="fa fa-times-circle"></i> ' + response.message;
                hideExcelPreview();
                disableExcelCompose();
            }
        },
        error: function() {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-upload"></i> Upload';
            resultDiv.style.display = 'block';
            resultDiv.className = 'result-box error';
            resultDiv.innerHTML = '<i class="fa fa-times-circle"></i> An error occurred while uploading the file';
        }
    });
}

function showExcelPreview(count, preview) {
    var previewDiv = document.getElementById('excel-recipients-preview');
    var countSpan = document.getElementById('excel-recipient-count');
    var tableBody = document.getElementById('excel-preview-table');
    var moreText = document.getElementById('excel-more-text');

    countSpan.textContent = count;

    // Build preview table
    var html = '';
    preview.forEach(function(recipient) {
        html += '<tr>';
        html += '<td style="padding: 0.5rem; border-bottom: 1px solid #e5e7eb;">' + escapeHtml(recipient.name) + '</td>';
        html += '<td style="padding: 0.5rem; border-bottom: 1px solid #e5e7eb;">' + escapeHtml(recipient.email) + '</td>';
        html += '</tr>';
    });
    tableBody.innerHTML = html;

    // Show "and X more" text
    if (count > 5) {
        moreText.textContent = '... and ' + (count - 5) + ' more recipients';
        moreText.style.display = 'block';
    } else {
        moreText.style.display = 'none';
    }

    previewDiv.style.display = 'block';
}

function hideExcelPreview() {
    document.getElementById('excel-recipients-preview').style.display = 'none';
}

function enableExcelCompose() {
    var section = document.getElementById('excel-compose-section');
    section.style.opacity = '1';
    section.style.pointerEvents = 'auto';
}

function disableExcelCompose() {
    var section = document.getElementById('excel-compose-section');
    section.style.opacity = '0.5';
    section.style.pointerEvents = 'none';
}

function clearExcelUpload() {
    document.getElementById('excel_file').value = '';
    document.getElementById('excel-upload-result').style.display = 'none';
    hideExcelPreview();
    disableExcelCompose();
    excelRecipientsLoaded = false;

    // Reset file input display
    document.getElementById('excel_file_label').style.display = 'block';
    document.getElementById('excel_file_selected').style.display = 'none';
    document.getElementById('excel_drop_zone').style.borderColor = '#d1d5db';
    document.getElementById('excel_drop_zone').style.background = '#f9fafb';

    // Clear session data on server
    $.post('<?php echo base_url("email/clear_excel_session"); ?>');
}

function loadExcelTemplate(index) {
    var template = emailTemplates[index];
    document.getElementById('excel_subject').value = template.subject;
    document.getElementById('excel_message').value = template.body;
}

function sendExcelBulk() {
    if (!excelRecipientsLoaded) {
        alert('Please upload an Excel file first');
        return;
    }

    // Sync visual editor content before sending
    syncEditorContent('excel');

    var subject = document.getElementById('excel_subject').value;
    var message = document.getElementById('excel_message').value;

    if (!subject || !message) {
        alert('Please enter subject and message');
        return;
    }

    var count = parseInt(document.getElementById('excel-recipient-count').textContent) || 0;
    if (!confirm('Are you sure you want to send this email to ' + count + ' recipients?')) {
        return;
    }

    var btn = document.getElementById('excel_send_btn');
    var result = document.getElementById('excel-result');
    var progress = document.getElementById('excel-progress');
    var progressBar = document.getElementById('excel-progress-bar');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Sending...';
    result.style.display = 'none';
    progress.style.display = 'block';
    progressBar.style.width = '0%';
    progressBar.textContent = 'Preparing...';

    // Simulate progress while sending
    var progressInterval = setInterval(function() {
        var current = parseInt(progressBar.style.width) || 0;
        if (current < 90) {
            current += Math.random() * 3;
            progressBar.style.width = current + '%';
            progressBar.textContent = Math.round(current) + '%';
        }
    }, 500);

    // Use FormData to support file attachments
    var formData = new FormData();
    formData.append('subject', subject);
    formData.append('message', message);
    formData.append('use_template', document.getElementById('excel_use_template').checked ? '1' : '0');
    formData.append('use_name', document.getElementById('excel_use_name').checked ? '1' : '0');

    // Add attachments
    var attachments = document.getElementById('excel_attachments').files;
    for (var i = 0; i < attachments.length; i++) {
        formData.append('attachments[]', attachments[i]);
    }

    $.ajax({
        url: '<?php echo base_url("email/send_excel_bulk"); ?>',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            clearInterval(progressInterval);
            progressBar.style.width = '100%';
            progressBar.textContent = '100%';

            setTimeout(function() {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send to All Recipients';
                progress.style.display = 'none';

                result.style.display = 'block';
                if (response.success) {
                    var html = '<i class="fa fa-check-circle"></i> ' + response.message;
                    if (response.errors && response.errors.length > 0) {
                        html += '<br><br><strong>Failed emails:</strong><ul style="margin: 0.5rem 0 0 1rem;">';
                        response.errors.forEach(function(err) {
                            html += '<li style="font-size: 0.85rem;">' + err + '</li>';
                        });
                        html += '</ul>';
                    }
                    result.className = 'result-box success';
                    result.innerHTML = html;

                    // Reset the form
                    clearExcelUpload();
                } else {
                    result.className = 'result-box error';
                    result.innerHTML = '<i class="fa fa-times-circle"></i> ' + response.message;
                }
            }, 500);
        },
        error: function() {
            clearInterval(progressInterval);
            btn.disabled = false;
            btn.innerHTML = '<i class="fa fa-paper-plane"></i> Send to All Recipients';
            progress.style.display = 'none';
            result.style.display = 'block';
            result.className = 'result-box error';
            result.innerHTML = '<i class="fa fa-times-circle"></i> An error occurred while sending';
        }
    });
}

// Helper function to escape HTML
function escapeHtml(text) {
    var div = document.createElement('div');
    div.appendChild(document.createTextNode(text || ''));
    return div.innerHTML;
}

// ============================================
// Visual Editor Functions
// ============================================

// Switch between Content and HTML mode
function switchEditorMode(prefix, mode) {
    var contentEditor = document.getElementById(prefix + '_content_editor');
    var htmlEditor = document.getElementById(prefix + '_message');
    var visualEditor = document.getElementById(prefix + '_visual_editor');
    var contentBtn = document.getElementById(prefix + '_content_btn');
    var htmlBtn = document.getElementById(prefix + '_html_btn');

    if (mode === 'html') {
        // Switching to HTML mode - copy content from visual editor
        htmlEditor.value = visualEditor.innerHTML;
        contentEditor.style.display = 'none';
        htmlEditor.style.display = 'block';
        contentBtn.classList.remove('active');
        htmlBtn.classList.add('active');
    } else {
        // Switching to Content mode - copy HTML to visual editor
        visualEditor.innerHTML = htmlEditor.value;
        htmlEditor.style.display = 'none';
        contentEditor.style.display = 'block';
        htmlBtn.classList.remove('active');
        contentBtn.classList.add('active');
    }
}

// Format text in visual editor
function formatText(prefix, format) {
    var editor = document.getElementById(prefix + '_visual_editor');
    editor.focus();

    switch(format) {
        case 'bold':
            document.execCommand('bold', false, null);
            break;
        case 'italic':
            document.execCommand('italic', false, null);
            break;
        case 'underline':
            document.execCommand('underline', false, null);
            break;
        case 'heading':
            document.execCommand('formatBlock', false, '<h3>');
            break;
        case 'paragraph':
            document.execCommand('formatBlock', false, '<p>');
            break;
        case 'ul':
            document.execCommand('insertUnorderedList', false, null);
            break;
        case 'ol':
            document.execCommand('insertOrderedList', false, null);
            break;
        case 'link':
            var url = prompt('Enter URL:', 'https://');
            if (url) {
                document.execCommand('createLink', false, url);
            }
            break;
    }

    // Sync to hidden textarea
    syncEditorContent(prefix);
}

// Insert variable at cursor position
function insertVariable(prefix, variable) {
    var editor = document.getElementById(prefix + '_visual_editor');
    editor.focus();
    document.execCommand('insertText', false, variable);
    syncEditorContent(prefix);
}

// Sync visual editor content to hidden textarea
function syncEditorContent(prefix) {
    var visualEditor = document.getElementById(prefix + '_visual_editor');
    var htmlEditor = document.getElementById(prefix + '_message');
    htmlEditor.value = visualEditor.innerHTML;
}

// Initialize editor sync on input
document.addEventListener('DOMContentLoaded', function() {
    ['test', 'excel'].forEach(function(prefix) {
        var visualEditor = document.getElementById(prefix + '_visual_editor');
        if (visualEditor) {
            visualEditor.addEventListener('input', function() {
                syncEditorContent(prefix);
            });
            visualEditor.addEventListener('blur', function() {
                syncEditorContent(prefix);
            });
        }
    });
});

// ============================================
// Attachment Handling Functions
// ============================================

// File input wrapper click handlers
document.querySelectorAll('.file-input-wrapper').forEach(function(wrapper) {
    wrapper.addEventListener('click', function() {
        var input = this.querySelector('input[type="file"]');
        if (input) input.click();
    });
});

// Test email attachments
document.getElementById('test_attachments').addEventListener('change', function() {
    displayAttachments('test', this.files);
    updateFileText('test', this.files);
});

// Excel email attachments
document.getElementById('excel_attachments').addEventListener('change', function() {
    displayAttachments('excel', this.files);
    updateFileText('excel', this.files);
});

function updateFileText(prefix, files) {
    var textEl = document.getElementById(prefix + '_file_text');
    if (textEl) {
        if (files.length === 0) {
            textEl.textContent = 'No files selected';
        } else if (files.length === 1) {
            textEl.textContent = files[0].name;
        } else {
            textEl.textContent = files.length + ' files selected';
        }
    }
}

function displayAttachments(prefix, files) {
    var listDiv = document.getElementById(prefix + '_attachment_list');
    listDiv.innerHTML = '';

    for (var i = 0; i < files.length; i++) {
        var file = files[i];
        var size = (file.size / 1024).toFixed(1);
        var sizeLabel = size > 1024 ? (size / 1024).toFixed(1) + ' MB' : size + ' KB';

        var item = document.createElement('div');
        item.className = 'attachment-item';
        item.innerHTML = '<i class="fa fa-file"></i> ' + escapeHtml(file.name) + ' <span style="color: #6b7280;">(' + sizeLabel + ')</span>';
        listDiv.appendChild(item);
    }
}

</script>

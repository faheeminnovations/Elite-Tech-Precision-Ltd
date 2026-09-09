<?php

namespace App\Services;

use App\Mail\ContractStatusMail;
use App\Mail\ResponseStatusMail;
use App\Mail\StatusUpdateMail;
use App\Models\EmailNotificationSetting;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Check if email notifications are enabled globally
     */
    public function isEnabled(): bool
    {
        // Temporarily always return true to ensure emails are sent
        // return session('settings.enable_email_notifications', true) === true;
        return true;
    }

    /**
     * Check if a specific email type is enabled
     */
    public function isEmailTypeEnabled(string $emailType): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }
        return EmailNotificationSetting::isEnabled($emailType);
    }

    /**
     * Send status update notification
     */
    public function sendStatusUpdate(string $entityType, string $entityName, string $oldStatus, string $newStatus, string $action, $user, $customerEmail = null): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new StatusUpdateMail(
            $entityType,
            $entityName,
            $oldStatus,
            $newStatus,
            $action,
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if provided
        if ($customerEmail && !empty($customerEmail)) {
            Mail::to($customerEmail)->send(new StatusUpdateMail(
                $entityType,
                $entityName,
                $oldStatus,
                $newStatus,
                $action,
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send contract created notification
     */
    public function sendContractCreated($contract, $user): void
    {
        if (!$this->isEmailTypeEnabled('contract_created')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new ContractStatusMail(
            $contract,
            'N/A',
            $contract->status ?? 'active',
            'Contract Created',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($contract->customer_email)) {
            Mail::to($contract->customer_email)->send(new ContractStatusMail(
                $contract,
                'N/A',
                $contract->status ?? 'active',
                'Contract Created',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send contract updated notification
     */
    public function sendContractUpdated($contract, $user): void
    {
        if (!$this->isEmailTypeEnabled('contract_updated')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new ContractStatusMail(
            $contract,
            $contract->status ?? 'N/A',
            $contract->status ?? 'N/A',
            'Contract Updated',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($contract->customer_email)) {
            Mail::to($contract->customer_email)->send(new ContractStatusMail(
                $contract,
                $contract->status ?? 'N/A',
                $contract->status ?? 'N/A',
                'Contract Updated',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send contract deleted notification
     */
    public function sendContractDeleted($contractData, $user): void
    {
        if (!$this->isEmailTypeEnabled('contract_deleted')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new ContractStatusMail(
            $contractData,
            $contractData->status ?? 'N/A',
            'Deleted',
            'Contract Deleted',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($contractData->customer_email)) {
            Mail::to($contractData->customer_email)->send(new ContractStatusMail(
                $contractData,
                $contractData->status ?? 'N/A',
                'Deleted',
                'Contract Deleted',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send contract status changed notification
     */
    public function sendContractStatusChanged($contract, string $oldStatus, string $newStatus, $user): void
    {
        if (!$this->isEmailTypeEnabled('contract_status_changed')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new ContractStatusMail(
            $contract,
            $oldStatus,
            $newStatus,
            'Status Changed',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($contract->customer_email)) {
            Mail::to($contract->customer_email)->send(new ContractStatusMail(
                $contract,
                $oldStatus,
                $newStatus,
                'Status Changed',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send customer created notification
     */
    public function sendCustomerCreated($customer, $user): void
    {
        if (!$this->isEmailTypeEnabled('customer_created')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new StatusUpdateMail(
            'Customer',
            $customer->name,
            'N/A',
            $customer->status ?? 'active',
            'Customer Created',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($customer->email)) {
            Mail::to($customer->email)->send(new StatusUpdateMail(
                'Customer',
                $customer->name,
                'N/A',
                $customer->status ?? 'active',
                'Customer Created',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send customer updated notification
     */
    public function sendCustomerUpdated($customer, $user): void
    {
        if (!$this->isEmailTypeEnabled('customer_updated')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new StatusUpdateMail(
            'Customer',
            $customer->name,
            $customer->status ?? 'N/A',
            $customer->status ?? 'N/A',
            'Customer Updated',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($customer->email)) {
            Mail::to($customer->email)->send(new StatusUpdateMail(
                'Customer',
                $customer->name,
                $customer->status ?? 'N/A',
                $customer->status ?? 'N/A',
                'Customer Updated',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send customer deleted notification
     */
    public function sendCustomerDeleted($customerData, $user): void
    {
        if (!$this->isEmailTypeEnabled('customer_deleted')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new StatusUpdateMail(
            'Customer',
            $customerData->name ?? 'Unknown',
            $customerData->status ?? 'N/A',
            'Deleted',
            'Customer Deleted',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($customerData->email)) {
            Mail::to($customerData->email)->send(new StatusUpdateMail(
                'Customer',
                $customerData->name ?? 'Unknown',
                $customerData->status ?? 'N/A',
                'Deleted',
                'Customer Deleted',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send customer status changed notification
     */
    public function sendCustomerStatusChanged($customer, string $oldStatus, string $newStatus, $user): void
    {
        if (!$this->isEmailTypeEnabled('customer_status_changed')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new StatusUpdateMail(
            'Customer',
            $customer->name,
            $oldStatus,
            $newStatus,
            'Status Changed',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($customer->email)) {
            Mail::to($customer->email)->send(new StatusUpdateMail(
                'Customer',
                $customer->name,
                $oldStatus,
                $newStatus,
                'Status Changed',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send service created notification
     */
    public function sendServiceCreated($service, $user): void
    {
        if (!$this->isEmailTypeEnabled('service_created')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new StatusUpdateMail(
            'Service',
            $service->name ?? 'Service #' . $service->id,
            'N/A',
            $service->status ?? 'active',
            'Service Created',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($service->customer_email)) {
            Mail::to($service->customer_email)->send(new StatusUpdateMail(
                'Service',
                $service->name ?? 'Service #' . $service->id,
                'N/A',
                $service->status ?? 'active',
                'Service Created',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send service updated notification
     */
    public function sendServiceUpdated($service, $user): void
    {
        if (!$this->isEmailTypeEnabled('service_updated')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new StatusUpdateMail(
            'Service',
            $service->name ?? 'Service #' . $service->id,
            $service->status ?? 'N/A',
            $service->status ?? 'N/A',
            'Service Updated',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($service->customer_email)) {
            Mail::to($service->customer_email)->send(new StatusUpdateMail(
                'Service',
                $service->name ?? 'Service #' . $service->id,
                $service->status ?? 'N/A',
                $service->status ?? 'N/A',
                'Service Updated',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send service deleted notification
     */
    public function sendServiceDeleted($serviceData, $user): void
    {
        if (!$this->isEmailTypeEnabled('service_deleted')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new StatusUpdateMail(
            'Service',
            $serviceData->name ?? 'Service #' . ($serviceData->id ?? 'Unknown'),
            $serviceData->status ?? 'N/A',
            'Deleted',
            'Service Deleted',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($serviceData->customer_email)) {
            Mail::to($serviceData->customer_email)->send(new StatusUpdateMail(
                'Service',
                $serviceData->name ?? 'Service #' . ($serviceData->id ?? 'Unknown'),
                $serviceData->status ?? 'N/A',
                'Deleted',
                'Service Deleted',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send response created notification
     */
    public function sendResponseCreated($response, $user): void
    {
        if (!$this->isEmailTypeEnabled('response_created')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new ResponseStatusMail(
            $response,
            'N/A',
            $response->status ?? 'active',
            'Response Created',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($response->customer_email)) {
            Mail::to($response->customer_email)->send(new ResponseStatusMail(
                $response,
                'N/A',
                $response->status ?? 'active',
                'Response Created',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send response updated notification
     */
    public function sendResponseUpdated($response, $user): void
    {
        if (!$this->isEmailTypeEnabled('response_updated')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new ResponseStatusMail(
            $response,
            $response->status ?? 'N/A',
            $response->status ?? 'N/A',
            'Response Updated',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($response->customer_email)) {
            Mail::to($response->customer_email)->send(new ResponseStatusMail(
                $response,
                $response->status ?? 'N/A',
                $response->status ?? 'N/A',
                'Response Updated',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send response deleted notification
     */
    public function sendResponseDeleted($responseData, $user): void
    {
        if (!$this->isEmailTypeEnabled('response_deleted')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new ResponseStatusMail(
            $responseData,
            $responseData->status ?? 'N/A',
            'Deleted',
            'Response Deleted',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($responseData->customer_email)) {
            Mail::to($responseData->customer_email)->send(new ResponseStatusMail(
                $responseData,
                $responseData->status ?? 'N/A',
                'Deleted',
                'Response Deleted',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send response status changed notification
     */
    public function sendResponseStatusChanged($response, string $oldStatus, string $newStatus, $user): void
    {
        if (!$this->isEmailTypeEnabled('response_status_changed')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = config('mail.from.address');
        Mail::to($adminEmail)->send(new ResponseStatusMail(
            $response,
            $oldStatus,
            $newStatus,
            'Status Changed',
            $user,
            true // isAdmin = true for admin email
        ));

        // Send separate email to customer if available
        if (!empty($response->customer_email)) {
            Mail::to($response->customer_email)->send(new ResponseStatusMail(
                $response,
                $oldStatus,
                $newStatus,
                'Status Changed',
                $user,
                false // isAdmin = false for customer email
            ));
        }
    }

    /**
     * Send customer PPM reminder
     */
    public function sendCustomerPPMReminder($contract, $customer, $daysUntilDue): void
    {
        if (!$this->isEmailTypeEnabled('customer_ppm_reminder')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = session('settings.internal_reminder_inbox') ?? config('mail.from.address');
        Mail::to($adminEmail)->send(new \App\Mail\PPMReminderMail(
            $contract,
            $customer,
            $daysUntilDue,
            'internal'
        ));

        // Send separate email to customer if available
        if (!empty($customer->email)) {
            Mail::to($customer->email)->send(new \App\Mail\PPMReminderMail(
                $contract,
                $customer,
                $daysUntilDue,
                'customer'
            ));
        }
    }

    /**
     * Send internal PPM reminder
     */
    public function sendInternalPPMReminder($contract, $customer, $daysUntilDue): void
    {
        if (!$this->isEmailTypeEnabled('internal_ppm_reminder')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = session('settings.internal_reminder_inbox') ?? config('mail.from.address');
        Mail::to($adminEmail)->send(new \App\Mail\PPMReminderMail(
            $contract,
            $customer,
            $daysUntilDue,
            'internal'
        ));

        // Send separate email to customer if available
        if (!empty($customer->email)) {
            Mail::to($customer->email)->send(new \App\Mail\PPMReminderMail(
                $contract,
                $customer,
                $daysUntilDue,
                'customer'
            ));
        }
    }

    /**
     * Send contract expiry reminder
     */
    public function sendContractExpiryReminder($contract, $customer, $daysUntilExpiry): void
    {
        if (!$this->isEmailTypeEnabled('contract_expiry_reminder')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = session('settings.internal_reminder_inbox') ?? config('mail.from.address');
        Mail::to($adminEmail)->send(new \App\Mail\ContractExpiryReminderMail(
            $contract,
            $customer,
            $daysUntilExpiry
        ));

        // Send separate email to customer if available
        if (!empty($customer->email)) {
            Mail::to($customer->email)->send(new \App\Mail\ContractExpiryReminderMail(
                $contract,
                $customer,
                $daysUntilExpiry
            ));
        }
    }

    /**
     * Send service due reminder
     */
    public function sendServiceDueReminder($service, $contract, $customer): void
    {
        if (!$this->isEmailTypeEnabled('service_due_reminder')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = session('settings.internal_reminder_inbox') ?? config('mail.from.address');
        Mail::to($adminEmail)->send(new \App\Mail\ServiceDueReminderMail(
            $service,
            $contract,
            $customer
        ));

        // Send separate email to customer if available
        if (!empty($customer->email)) {
            Mail::to($customer->email)->send(new \App\Mail\ServiceDueReminderMail(
                $service,
                $contract,
                $customer
            ));
        }
    }

    /**
     * Send overdue contract reminder
     */
    public function sendOverdueContractReminder($contract, $customer, $daysOverdue): void
    {
        if (!$this->isEmailTypeEnabled('overdue_contract_reminder')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = session('settings.internal_reminder_inbox') ?? config('mail.from.address');
        Mail::to($adminEmail)->send(new \App\Mail\OverdueContractReminderMail(
            $contract,
            $customer,
            $daysOverdue
        ));

        // Send separate email to customer if available
        if (!empty($customer->email)) {
            Mail::to($customer->email)->send(new \App\Mail\OverdueContractReminderMail(
                $contract,
                $customer,
                $daysOverdue
            ));
        }
    }

    /**
     * Send response pending reminder
     */
    public function sendResponsePendingReminder($response, $contract, $customer, $daysPending): void
    {
        if (!$this->isEmailTypeEnabled('response_pending_reminder')) {
            return;
        }

        // Always send to admin/internal email
        $adminEmail = session('settings.internal_reminder_inbox') ?? config('mail.from.address');
        Mail::to($adminEmail)->send(new \App\Mail\ResponsePendingReminderMail(
            $response,
            $contract,
            $customer,
            $daysPending
        ));

        // Send separate email to customer if available
        if (!empty($customer->email)) {
            Mail::to($customer->email)->send(new \App\Mail\ResponsePendingReminderMail(
                $response,
                $contract,
                $customer,
                $daysPending
            ));
        }
    }

    /**
     * Get notification recipient email
     */
    public function getRecipientEmail(): string
    {
        return session('settings.internal_reminder_inbox') ?? config('mail.from.address');
    }

    /**
     * Send contract status notification (backward compatibility)
     */
    public function sendContractStatus($contract, string $oldStatus, string $newStatus, string $action, $user): void
    {
        // Delegate to the specific method based on action
        switch ($action) {
            case 'Contract Created':
                $this->sendContractCreated($contract, $user);
                break;
            case 'Contract Updated':
                $this->sendContractUpdated($contract, $user);
                break;
            case 'Contract Deleted':
                $this->sendContractDeleted($contract, $user);
                break;
            case 'Status Update':
            case 'Status Changed':
                $this->sendContractStatusChanged($contract, $oldStatus, $newStatus, $user);
                break;
            default:
                // Fall back to the original behavior for backward compatibility
                if (!$this->isEnabled()) {
                    return;
                }
                // Always send to admin/internal email
                $adminEmail = session('settings.internal_reminder_inbox') ?? config('mail.from.address');
                Mail::to($adminEmail)->send(new ContractStatusMail(
                    $contract,
                    $oldStatus,
                    $newStatus,
                    $action,
                    $user,
                    true // isAdmin = true for admin email
                ));

                // Send separate email to customer if available
                if (!empty($contract->customer_email)) {
                    Mail::to($contract->customer_email)->send(new ContractStatusMail(
                        $contract,
                        $oldStatus,
                        $newStatus,
                        $action,
                        $user,
                        false // isAdmin = false for customer email
                    ));
                }
        }
    }

    /**
     * Send response status notification (backward compatibility)
     */
    public function sendResponseStatus($response, string $oldStatus, string $newStatus, string $action, $user): void
    {
        // Delegate to the specific method based on action
        switch ($action) {
            case 'Response Created':
                $this->sendResponseCreated($response, $user);
                break;
            case 'Response Updated':
                $this->sendResponseUpdated($response, $user);
                break;
            case 'Response Deleted':
                $this->sendResponseDeleted($response, $user);
                break;
            case 'Status Update':
            case 'Status Changed':
                $this->sendResponseStatusChanged($response, $oldStatus, $newStatus, $user);
                break;
            default:
                // Fall back to the original behavior for backward compatibility
                if (!$this->isEnabled()) {
                    return;
                }
                // Always send to admin/internal email
                $adminEmail = session('settings.internal_reminder_inbox') ?? config('mail.from.address');
                Mail::to($adminEmail)->send(new ResponseStatusMail(
                    $response,
                    $oldStatus,
                    $newStatus,
                    $action,
                    $user,
                    true // isAdmin = true for admin email
                ));

                // Send separate email to customer if available
                if (!empty($response->customer_email)) {
                    Mail::to($response->customer_email)->send(new ResponseStatusMail(
                        $response,
                        $oldStatus,
                        $newStatus,
                        $action,
                        $user,
                        false // isAdmin = false for customer email
                    ));
                }
        }
    }
}
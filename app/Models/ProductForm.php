<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductForm extends Model
{
    protected $fillable = [
        'form_name',
        'product_id',
        'redirect_url',
        'button_text',
        'packages',
        'generated_form',
        'is_active',
    ];

    protected $casts = [
        'packages' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product that owns the form.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Generate the HTML form based on the form configuration
     */
    public function generateFormHtml(): string
    {
        $packages = $this->packages ?? [];
        $nigerianStates = [
            'Abia', 'Adamawa', 'Akwa Ibom', 'Anambra', 'Bauchi', 'Bayelsa',
            'Benue', 'Borno', 'Cross River', 'Delta', 'Ebonyi', 'Edo',
            'Ekiti', 'Enugu', 'FCT', 'Gombe', 'Imo', 'Jigawa', 'Kaduna',
            'Kano', 'Katsina', 'Kebbi', 'Kogi', 'Kwara', 'Lagos', 'Nasarawa',
            'Niger', 'Ogun', 'Ondo', 'Osun', 'Oyo', 'Plateau', 'Rivers',
            'Sokoto', 'Taraba', 'Yobe', 'Zamfara'
        ];

        $packageOptions = '';
        foreach ($packages as $package) {
            if (!empty($package['name']) && !empty($package['price'])) {
                $packageOptions .= '<option value="' . $package['id'] . '">' .
                    htmlspecialchars($package['name']) . ' - ₦' .
                    number_format($package['price'], 2) . '</option>' . "\n";
            }
        }

        $stateOptions = '';
        foreach ($nigerianStates as $state) {
            $stateOptions .= '<option value="' . $state . '">' . $state . '</option>' . "\n";
        }

        $html = '<form method="POST" action="' . route('external.order.submit') . '" style="max-width: 600px; margin: auto;" id="orderForm">
  <input type="hidden" name="form_id" value="' . $this->id . '">

  <label>Full Name <span style="color: red">*</span></label>
  <input type="text" name="full_name" placeholder="First Name & Last Name" required style="width: 100%; padding: 10px; margin-bottom: 10px;" />

  <label>Phone Number <span style="color: red">*</span></label>
  <input type="tel" name="phone_number" placeholder="07034567890" required style="width: 100%; padding: 10px; margin-bottom: 10px;" />

  <label>WhatsApp Number</label>
  <input type="tel" name="whatsapp_number" placeholder="0701234..." style="width: 100%; padding: 10px; margin-bottom: 10px;" />

  <label>Email Address</label>
  <input type="email" name="email" placeholder="customer@example.com" style="width: 100%; padding: 10px; margin-bottom: 10px;" />

  <label>Select your package <span style="color: red">*</span></label>
  <select name="package" required style="width: 100%; padding: 10px; margin-bottom: 10px;">
    <option value="">-- Select Package --</option>
    ' . $packageOptions . '
  </select>

  <label>Delivery State <span style="color: red">*</span></label>
  <select name="state" required style="width: 100%; padding: 10px; margin-bottom: 10px;">
    <option value="">-- Select State --</option>
    ' . $stateOptions . '
  </select>

  <label>Delivery Address <span style="color: red">*</span></label>
  <textarea name="address" placeholder="Delivery Address" required style="width: 100%; padding: 10px; margin-bottom: 20px;"></textarea>

  <button type="submit" id="submitBtn" style="width: 100%; background-color: green; color: white; padding: 15px; border: none; border-radius: 30px; font-size: 18px;">
    ' . htmlspecialchars($this->button_text) . '
  </button>
</form>
<script src="' . config('app.url') . '/assets/js/click_control.js"></script>';

        return $html;
    }

    /**
     * Copy the generated form to clipboard (JavaScript function)
     */
    public function getCopyFormScript(): string
    {
        $formHtml = $this->generateFormHtml();
        $escapedHtml = addslashes(str_replace(["\n", "\r"], '', $formHtml));

        return "copyFormToClipboard('" . $escapedHtml . "')";
    }
}

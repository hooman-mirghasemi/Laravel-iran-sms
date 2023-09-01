<?php

namespace HoomanMirghasemi\Sms\Models;

use HoomanMirghasemi\Sms\Database\Factories\SmsReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * App\Models\SmsReport.
 *
 * @property int                             $id                   identifier
 * @property string|null                     $mobile               mobile number sms sent to
 * @property string|null                     $message              sms text
 * @property string|null                     $from                 name of sms sender provider
 * @property string|null                     $number               sender number
 * @property string|null                     $web_service_response sms provider webservice response
 * @property bool                            $success              boolean: successful webservice response or fail
 * @property \Illuminate\Support\Carbon|null $created_at           when this record created
 * @property \Illuminate\Support\Carbon|null $updated_at           the record last updated time
 *
 * @method static \Illuminate\Database\Eloquent\Builder|SmsReport newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsReport newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SmsReport query()
 *
 * @mixin \Eloquent
 */
class SmsReport extends \Illuminate\Database\Eloquent\Model
{
    use HasFactory;

    public const TABLE = 'sms_reports';

    /**
     * The model table name in database.
     *
     * @var string
     */
    protected $table = self::TABLE;

    /**
     * An array of attribute names that are mass assignable in the database.
     *
     * @var string[]
     */
    protected $fillable = [
        'cell',
        'message',
        'from',
        'number',
        'web_service_response',
        'success',
    ];

    protected $casts = [
        'success' => 'boolean',
    ];

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return new SmsReportFactory();
    }
}

<?php

namespace App\Models\Users;

use App\Helpers\UrlHelper;
use Illuminate\Database\Eloquent\Model;

class UserPortfolioDetail extends Model
{
    protected $table = 'user_portfolio_details';

    protected $fillable = [
        'tagline',
        'user_id',
        'background_cover_url',
        'primary_phone',
        'github_url',
        'linkedin_url',
        'resume_url',
        'interests',
    ];

    const FIELDS_WITH_NO_SCHEME = [
        'github_url',
        'linkedin_url',
        'resume_url'
    ];

    public static function findOrEmptyByUser(int $userId): self
    {

        $detail = self::where('user_id', $userId)
            ->first();

        if (empty($detail)) {
            $detail = new self;
        }

        return $detail;
    }

    /**
     * @param array $requestArr
     */
    public static function sanitizeFields(array &$requestArr)
    {

        $urlHelper = new UrlHelper();
        /**
         * remove schemes on url when storing on db
         */
        foreach (self::FIELDS_WITH_NO_SCHEME as $field) {
            $reqValue = $requestArr[$field] ?? null;
            if (!empty($reqValue)) {
                $requestArr[$field] = $urlHelper->removeScheme($reqValue);
            }
        }

        return $requestArr;
    }
}
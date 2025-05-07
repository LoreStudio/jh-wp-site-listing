<?php

namespace StudySiteListing\Config;

class Location_Config {
    /**
     * Get active countries
     */
    public static function get_countries(): array {
        // Only load countries that are actually used in the database
        global $wpdb;
        $used_countries = $wpdb->get_col(
            "SELECT DISTINCT meta_value 
            FROM {$wpdb->postmeta} 
            WHERE meta_key = 'country' 
            AND meta_value != ''"
        );

        if (empty($used_countries)) {
            return [
                'US' => 'United States',
                'CA' => 'Canada',
                'GB' => 'United Kingdom'
            ];
        }

        // Load only used countries
        return self::filter_countries($used_countries);
    }

    /**
     * Get active languages
     */
    public static function get_languages(): array {
        // Check if WPML is active
        if (defined('ICL_SITEPRESS_VERSION')) {
            return self::get_wpml_languages();
        }

        // Get languages from options
        $active_languages = get_option('store-loc-active-languages', 'en');
        $language_codes = explode(',', $active_languages);

        return array_intersect_key(self::get_default_languages(), array_flip($language_codes));
    }

    /**
     * Get WPML languages
     */
    private static function get_wpml_languages(): array {
        global $sitepress;
        if (!$sitepress) {
            return ['en' => ['name' => 'English', 'flag' => 'us.png']];
        }

        $languages = [];
        $active_langs = $sitepress->get_active_languages();
        
        foreach ($active_langs as $code => $lang) {
            $languages[$code] = [
                'name' => $lang['display_name'],
                'flag' => $lang['country_flag_url']
            ];
        }

        return $languages;
    }

    /**
     * Filter countries list to only include used ones
     */
    private static function filter_countries(array $used_country_codes): array {
        $all_countries = [
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
            // Add more as needed, but only commonly used ones
            'FR' => 'France',
            'DE' => 'Germany',
            'ES' => 'Spain',
            'IT' => 'Italy',
            'AU' => 'Australia',
            'NZ' => 'New Zealand',
            'JP' => 'Japan',
            'CN' => 'China',
            'IN' => 'India'
        ];

        return array_intersect_key($all_countries, array_flip($used_country_codes));
    }

    /**
     * Get default languages
     */
    private static function get_default_languages(): array {
        return [
            'en' => ['name' => 'English', 'flag' => 'us.png'],
            'es' => ['name' => 'Spanish', 'flag' => 'es.svg'],
            'fr' => ['name' => 'French', 'flag' => 'fr.svg'],
            'de' => ['name' => 'German', 'flag' => 'de.svg'],
            'zh' => ['name' => 'Chinese', 'flag' => 'cn.svg'],
            // Add more as needed
        ];
    }
} 
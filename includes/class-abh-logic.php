<?php
if (!defined('ABSPATH')) exit;

class ABH_Logic {

    public static function get_holidays_api() {
        $year = date('Y');
        $transient_key = 'abh_holidays_' . $year;
        
        $holidays = get_transient($transient_key);

        if (false === $holidays) {
            // Aumentamos el timeout por si la API es lenta
            $response = wp_remote_get("https://apis.digital.gob.cl/fl/feriados/{$year}", array('timeout' => 10));
            
            if (is_wp_error($response)) {
                return []; 
            }

            $body = wp_remote_retrieve_body($response);
            $data = json_decode($body, true);
            
            $holidays = [];
            if (!empty($data) && is_array($data)) {
                foreach ($data as $h) {
                    $holidays[] = $h['fecha']; 
                }
            }
            // Cache por 24 horas
            set_transient($transient_key, $holidays, 24 * HOUR_IN_SECONDS);
        }
        
        return $holidays;
    }

    public static function get_current_status() {
        // CORRECCIÓN: Usamos DateTime con zona horaria específica sin afectar al global
        $timezone = new DateTimeZone('America/Santiago');
        $now = new DateTime('now', $timezone);
        
        $today_date = $now->format('Y-m-d');
        $current_time = $now->format('H:i');
        // 'N' devuelve 1 (lunes) a 7 (domingo), útil para mapear si quisieras
        $day_keyword = strtolower($now->format('l')); // 'monday', 'tuesday'...

        // 1. Verificar Feriados
        $holidays = self::get_holidays_api();
        if (in_array($today_date, $holidays)) {
            return ['status' => 'closed', 'msg' => 'Cerrado (Feriado)', 'class' => 'holiday'];
        }

        // 2. Verificar Cierre Manual
        if (get_option("abh_{$day_keyword}_closed")) {
            return ['status' => 'closed', 'msg' => 'Cerrado hoy', 'class' => 'closed'];
        }

        // 3. Verificar Horarios
        $start1 = get_option("abh_{$day_keyword}_start");
        $end1   = get_option("abh_{$day_keyword}_end");
        
        $start2 = get_option("abh_{$day_keyword}_start_2");
        $end2   = get_option("abh_{$day_keyword}_end_2");

        $is_open = false;

        if ($start1 && $end1) {
            if ($current_time >= $start1 && $current_time <= $end1) {
                $is_open = true;
            }
        }

        if (!$is_open && $start2 && $end2) {
            if ($current_time >= $start2 && $current_time <= $end2) {
                $is_open = true;
            }
        }

        if ($is_open) {
            return ['status' => 'open', 'msg' => 'Abierto', 'class' => 'open'];
        }

        return ['status' => 'closed', 'msg' => 'Cerrado', 'class' => 'closed'];
    }
}
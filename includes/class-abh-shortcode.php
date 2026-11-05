<?php
if (!defined('ABSPATH')) exit;

class ABH_Shortcode {

    public function __construct() {
        add_shortcode('abh_hours', array($this, 'render'));
    }

    public function render($atts) {
        $status = ABH_Logic::get_current_status();
        $holidays = ABH_Logic::get_holidays_api();

        // Mapeo ordenado explícitamente para iterar
        $days_map = [
            'monday'    => 'Lunes',
            'tuesday'   => 'Martes',
            'wednesday' => 'Miércoles',
            'thursday'  => 'Jueves',
            'friday'    => 'Viernes',
            'saturday'  => 'Sábado',
            'sunday'    => 'Domingo'
        ];
        
        $timezone_label = "Santiago de Chile"; 

        // Configurar zona horaria local SOLO para objetos DateTime
        $tz = new DateTimeZone('America/Santiago');
        $now = new DateTime('now', $tz);
        $current_day_keyword = strtolower($now->format('l'));

        // Calcular el Lunes de esta semana para iterar correctamente
        // Si hoy es lunes, tomamos hoy. Si no, volvemos al último lunes.
        $monday_this_week = clone $now;
        $monday_this_week->modify('this week monday'); 
        // Nota: En PHP 'this week monday' a veces falla si start_of_week no es lunes.
        // Una forma segura es:
        if ($now->format('N') != 1) {
            $monday_this_week->modify('last monday');
        }

        ob_start();
        ?>
        <div class="abh-widget-card">
            <h4 class="abh-title">Horarios de Atención</h4>
            
            <div class="abh-timezone-header">
                <small><i class="dashicons dashicons-globe"></i> Zona: <?php echo $timezone_label; ?></small>
            </div>
            
            <div class="abh-badge <?php echo $status['class']; ?>">
                <?php echo $status['msg']; ?>
            </div>

            <ul class="abh-list">
                <?php 
                // Iteramos 0 a 6 (Lunes a Domingo)
                $day_iterator = clone $monday_this_week;

                foreach($days_map as $key_day => $label): 
                    
                    // La fecha real de este día en la iteración
                    $day_date_str = $day_iterator->format('Y-m-d');
                    
                    // Verificamos si ES feriado
                    $is_holiday_today = in_array($day_date_str, $holidays);

                    // Obtenemos opciones
                    $is_closed_manual = get_option("abh_{$key_day}_closed");
                    $start1 = get_option("abh_{$key_day}_start");
                    $end1   = get_option("abh_{$key_day}_end");
                    $start2 = get_option("abh_{$key_day}_start_2");
                    $end2   = get_option("abh_{$key_day}_end_2");
                    
                    $time_html = '';

                    // LÓGICA DE PRIORIDAD
                    if ($is_holiday_today) {
                        // AQUÍ es donde se muestra el feriado en la lista
                        $time_html = '<span class="holiday-text">Feriado</span>';
                    } 
                    elseif ($is_closed_manual) {
                        $time_html = '<span class="closed-text">Cerrado</span>';
                    } 
                    elseif ($start1 && $end1) {
                        $time_html .= date("H:i", strtotime($start1)) . "-" . date("H:i", strtotime($end1));
                        
                        if ($start2 && $end2) {
                            $time_html .= ' <span class="separator">/</span> ' . date("H:i", strtotime($start2)) . "-" . date("H:i", strtotime($end2));
                        }
                    } else {
                        $time_html = '<span class="closed-text">Cerrado</span>';
                    }

                    $today_class = ($key_day === $current_day_keyword) ? 'is-today' : '';
                ?>
                    <li class="<?php echo $today_class; ?>">
                        <span class="day-label"><?php echo $label; ?></span> <span class="time-label"><?php echo $time_html; ?></span>
                    </li>
                <?php 
                    // Avanzar al siguiente día
                    $day_iterator->modify('+1 day');
                endforeach; 
                ?>
            </ul>
            
            <?php if($msg = get_option('abh_custom_msg')): ?>
                <div class="abh-msg"><?php echo esc_html($msg); ?></div>
            <?php endif; ?>

        </div>
        <?php
        return ob_get_clean();
    }
}
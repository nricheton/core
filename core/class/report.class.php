<?php

/* This file is part of Jeedom.
 *
 * Jeedom is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Jeedom is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
 */

/* * ***************************Includes********************************* */
require_once dirname(__FILE__) . '/../../core/php/core.inc.php';

class report {
	/*     * *************************Attributs****************************** */

	/*     * ***********************Methode static*************************** */
	public static function clean() {
		if (!file_exists(dirname(__FILE__) . '/../../data/report')) {
			return;
		}
		shell_exec('find ' . dirname(__FILE__) . '/../../data/report -type f -mtime +' . config::byKey('report::maxdays') . ' -delete');
	}

	public static function generate($_url, $_type, $_name, $_format = 'pdf', $_parameter = array()) {
		$out = dirname(__FILE__) . '/../../data/report/';
		$out .= $_type . '/';
		$out .= $_name . '/';
		if (!file_exists($out)) {
			mkdir($out, 0775, true);
		}
		$out .= date('Y_m_d_H_i_s') . '.' . $_format;
		$min_width = (isset($_parameter['width']) && $_parameter['width'] > 800) ? $_parameter['width'] : 1280;
		$min_height = (isset($_parameter['height']) && $_parameter['height'] > 600) ? $_parameter['height'] : 1280;
		$delay = (isset($_parameter['delay']) && $_parameter['delay'] > 1000) ? $_parameter['delay'] : config::byKey('report::delay');
		$_url .= '&auth=' . user::getAccessKeyForReport();
		$cmd = 'xvfb-run --server-args="-screen 0, 1920x1280x24" cutycapt --min-width=' . $min_width . ' --min-height=' . $min_height . ' --url="' . $_url . '" --out="' . $out . '"';
		$cmd .= ' --delay=' . $delay;
		$cmd .= ' --print-backgrounds=on';
		log::add('report', 'debug', $cmd);
		com_shell::execute($cmd);
		return $out;
	}

}
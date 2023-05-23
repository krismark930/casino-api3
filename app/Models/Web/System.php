<?php

namespace App\Models\Web;

use Illuminate\Database\Eloquent\Model;

class System extends Model {

    protected $fillable = [
        'R', 'OU', 'M', 'RE', 'ROU', 'PD', 'T', 'F', 'P', 'PR', 'P3', 'FS', 'MAX',
        'udp_ft_tw', 'udp_ft_r', 'udp_ft_v', 'udp_ft_re', 'udp_ft_pd', 'udp_ft_t', 'udp_ft_f', 'udp_ft_p', 'udp_ft_pr',
        'udp_bk_tw', 'udp_bk_r', 'udp_bk_rq', 'udp_bk_re', 'udp_bk_pr', 'udp_fs_fs',
        'udp_bs_tw', 'udp_bs_r', 'udp_bs_v', 'udp_bs_re', 'udp_bs_pd', 'udp_bs_t', 'udp_bs_m', 'udp_bs_p', 'udp_bs_pr',
        'udp_tn_tw', 'udp_tn_r', 'udp_tn_re', 'udp_tn_pd', 'udp_tn_p', 'udp_tn_pr',
        'udp_vb_tw', 'udp_vb_r', 'udp_vb_re', 'udp_vb_pd', 'udp_vb_p', 'udp_vb_pr',
        'udp_op_tw', 'udp_op_r', 'udp_op_v', 'udp_op_re', 'udp_op_pd', 'udp_op_t', 'udp_op_f', 'udp_op_p', 'udp_op_pr',
        'udp_ft_results', 'udp_ft_score', 'udp_bk_results', 'udp_bk_score', 'udp_bs_results', 'udp_bs_score',
        'udp_tn_results', 'udp_tn_score', 'udp_vb_results', 'udp_vb_score', 'udp_op_results', 'udp_op_score'
    ];
    protected $hidden = [
        'password',
    ];
    protected $table = 'web_system_data';
    public $timestamps = true;
}

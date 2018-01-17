<?php

namespace CodeFlix\Models;

use Bootstrapper\Interfaces\TableInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * CodeFlix\Models\Serie
 *
 * @property int $id
 * @property string $title
 * @property string $description
 * @property string $thumb
 * @property \Carbon\Carbon|null $created_at
 * @property \Carbon\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\CodeFlix\Models\Serie whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\CodeFlix\Models\Serie whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\CodeFlix\Models\Serie whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\CodeFlix\Models\Serie whereThumb($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\CodeFlix\Models\Serie whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\CodeFlix\Models\Serie whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Serie extends Model implements TableInterface
{
    protected $fillable = ['title', 'description'];

    public function videos(){
        return $this->hasMany(Video::class);
    }

    /**
     * @return array
     */
    public function getTableHeaders()
    {
        return ['#', 'Título', 'Descrição'];
    }

    /**
     * @param string $header
     * @return mixed
     */
    public function getValueForHeader($header)
    {
        switch ($header){
            case '#':
                return $this->id;
            case 'Título':
                return $this->title;
            case 'Descrição':
                return $this->description;
        }
    }

}
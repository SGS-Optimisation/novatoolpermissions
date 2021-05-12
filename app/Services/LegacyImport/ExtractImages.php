<?php


namespace App\Services\LegacyImport;


use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ExtractImages
{

    public $matches;

    public $to_replace = [];
    public $replacements = [];

    public $original_content;

    public $updated_content;

    /**
     * ExtractImages constructor.
     * @param $original_content
     */
    public function __construct($original_content = null)
    {
        $this->original_content = $original_content;
        $this->updated_content = $original_content;
    }


    public function handle($content = null)
    {
        if ($content) {
            $this->original_content = $content;
            $this->updated_content = $content;
        }

        if (!Str::contains($this->original_content, 'data:image')) {
            return $this;
        }

        $search_pattern = '/src="(data:image\/[^;]+;base64[^"]+)"/i';

        preg_match_all($search_pattern, $this->original_content, $this->matches);

        //array_shift($this->matches); // eliminate string
        if(empty($this->matches)) {
            return $this;
        }

        for ($i = 0; $i < count($this->matches[0]); $i++) {

            $matched = $this->matches[0][$i];
            $captured = $this->matches[1][$i];

            $this->to_replace[] = $matched;

            $base64_data_parts = explode(',', $captured);

            if (Str::contains($base64_data_parts[0], 'png')) {
                $extension = 'png';
            } else {
                $extension = 'jpg';
            }

            $image_name = uniqid().'.'.$extension;
            $image_path = 'rules/'.Carbon::now()->format('Y-m-d').'/'.$image_name;

            Storage::put(
                    $image_path,
                    base64_decode($base64_data_parts[1])
                );

            $this->replacements[] = 'src="'.Storage::url($image_path).'"';
        }



        for ($i = 0; $i < count($this->to_replace); $i++) {
            $find = $this->to_replace[$i];
            $replace = $this->replacements[$i];

            $this->updated_content = str_replace($find, $replace, $this->updated_content);
        }

        //$this->updated_content = preg_replace($this->to_replace, $this->replacements, $this->original_content);

        return $this;
    }


}

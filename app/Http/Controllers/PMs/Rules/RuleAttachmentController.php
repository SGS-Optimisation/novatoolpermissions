<?php

namespace App\Http\Controllers\PMs\Rules;

use App\Events\Rules\RuleUpdated;
use App\Http\Controllers\Controller;
use App\Models\Rule;
use Bnb\Laravel\Attachments\Attachment;
use Bnb\Laravel\Attachments\Contracts\AttachmentContract;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class RuleAttachmentController extends Controller
{

    /**
     * Attachment model
     *
     * @var AttachmentContract
     */
    protected $model;

    public function __construct(AttachmentContract $model)
    {
        $this->model = $model;
    }

    /**
     * @param  Request  $request
     * @param $client_account_slug
     * @param  int  $id
     */
    public function attach(Request $request, $client_account_slug, $id)
    {
        if (\Event::dispatch('attachments.dropzone.uploading', [$request], true) === false) {
            return response(\Lang::get('attachments::messages.errors.upload_denied'), 403);
        }

        logger('attachment for rule '.$id);

        /** @var Attachment $file */
        $file = $this->model
            ->fill(
                array_merge(
                    ['disk' => 'azure_docs'],
                    Arr::only(
                        $request->input(),
                        config('attachments.attributes')
                    ))
            )
            ->fromPost($request->file($request->input('file_key', 'file')));

        $file->metadata = ['dz_session_key' => csrf_token()];

        try {
            if ($file->save()) {
                $rule = Rule::find($id);
                Attachment::attach($file->uuid, $rule);
                //return Arr::only($file->toArray(), config('attachments.dropzone_attributes'));
                return $file;
            }
        } catch (\Exception $e) {
            logger('Failed to upload attachment : '.$e->getMessage(), ['trace' => $e->getTraceAsString()]);
        }

        return response(\Lang::get('attachments::messages.errors.upload_failed'), 500);
    }

    public function delete(Request $request, $client_account_slug, $id, $attachment)
    {
        $attachment = Attachment::find($attachment);
        $attachment->delete();

        return $request->wantsJson()
            ? new JsonResponse('', 200)
            : back()->with('status', 'rule-attachment-deleted');
    }
}

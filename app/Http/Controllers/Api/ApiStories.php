<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Auth\ApiController;
use App\Models\Stories\Story;
use App\Product;
use App\User;
use ErrorException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ApiStories extends ApiController
{

    /**
     * Create / Update a story
     * @return JsonResponse
     */
    public function upsert()
    {
        return $this->responseHandler(function () {
            /**
             * @var User $user
             * @var Story $story
             */

            $request = request();
            $id = $request->id ?? null;

            $this->validate($request, [
                'title' => 'required|string|max:255',
                'is_active' => 'required|in:1,0',
                'description' => 'string|max:512',
            ]);

            $user = auth()->user();

            $updatedMode = false;

            if (!empty($id)) {

                $updatedMode = true;

                if (!$story = Story::query()->find($id)) {
                    throw new ErrorException("Story not found");
                }

                if (!$story->hasAccess($user)) {
                    throw new ErrorException("Forbidden");
                }

            } else {
                $story = new Story();
            }

            $data = $request->all();
            $data['owner_user_id'] = $user->id;
            $data['slug'] = md5(time() . $user->id . rand(0, 1000));

            $story->fill($data);
            $story->save();

            $verbiage = $updatedMode ? "updated" : "added a new";

            $this->setSuccessMessage("Successfully {$verbiage} product.");

            return $story;
        });
    }

    /**
     * Returns all stories for currently logged in users
     * @return JsonResponse
     */
    public function all()
    {
        return $this->responseHandler(function () {
            return Story::query()
                ->where('owner_user_id', auth()->id())
                ->paginate();
        });
    }

    public function one($storyId)
    {
        return $this->responseHandler(function () use ($storyId) {

            if (empty($storyId) || !is_numeric($storyId)) {
                throw new InvalidArgumentException("Story id invalid");
            }

            /** @var Story $story */
            $story = Story::query()->findOrFail($storyId ?? null);

            if (!$story->hasAccess(auth()->user())) {
                throw new InvalidArgumentException("Forbidden..");
            }

            $addedProducts = $story->products;

            return [
                'story' => $story,
                'added_products' => $addedProducts,
                // get my products that have not been added yet
                'products' => Product::query()
                    ->where('products.owner_user_id', auth()->id())
                    ->whereNotIn('products.id', $addedProducts->pluck('id'))
                    ->get(),
            ];


        });
    }


    //todo: chris - refactor to separate controller
    public function slug($slug)
    {

        return $this->responseHandler(function () use ($slug) {

            if (empty($slug)) {
                throw new InvalidArgumentException("Slug invalid");
            }

            /** @var Story $story */
            $story = Story::query()->where('slug', $slug)->first();

            return [
                'story' => $story,
                'products' => $story->products,
            ];
        });

    }


}
<?php

namespace Ingenius\Banners\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Ingenius\Banners\Actions\DeleteBannerAction;
use Ingenius\Banners\Actions\PaginateBannersAction;
use Ingenius\Banners\Actions\StoreBannerAction;
use Ingenius\Banners\Actions\UpdateBannerAction;
use Ingenius\Banners\ContentTypes\ContentTypeFactory;
use Ingenius\Banners\Enums\BannerPlacement;
use Ingenius\Banners\Enums\BannerTarget;
use Ingenius\Banners\Http\Requests\StoreBannerRequest;
use Ingenius\Banners\Http\Requests\UpdateBannerRequest;
use Ingenius\Banners\Models\Banner;
use Ingenius\Banners\Services\BannersDispatcher;
use Ingenius\Banners\Transformers\EditViewBannerResource;
use Ingenius\Banners\Transformers\PublicViewBannerResource;
use Ingenius\Core\Helpers\AuthHelper;
use Ingenius\Core\Http\Controllers\Controller;

class BannersController extends Controller {

    use AuthorizesRequests;

    public function index(Request $request, PaginateBannersAction $action): JsonResponse {

        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'viewAny', Banner::class);

        $paginated = $action->handle($request->all());

        return Response::api(
            __('Banners fetched successfully'),
            $paginated
        );
    }

    public function show(Banner $banner): JsonResponse {

        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'view', $banner);

        return Response::api(
            __('Banner fetched successfully'),
            $banner->with([
                'placements',
                'contents',
                'targets'
            ])->first()
        );
    }

    public function store(StoreBannerRequest $request, StoreBannerAction $action): JsonResponse {
        
        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'create', Banner::class);

        $banner = $action->handle($request->validated());

        return Response::api(
            __('Banner created successfully'),
            $banner,
            201
        );
    }

    public function editView(Banner $banner): JsonResponse {

        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'view', $banner);

        return Response::api(
            __('Banner data for edit/view fetched successfully'),
            new EditViewBannerResource($banner->load('placements', 'targets', 'contents'))
        );
    }

    public function update(UpdateBannerRequest $request, Banner $banner, UpdateBannerAction $action): JsonResponse {

        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'update', $banner);

        $updatedBanner = $action->handle($banner, $request->validated());

        return Response::api(
            __('Banner updated successfully'),
            $updatedBanner
        );
    }

    public function destroy(Banner $banner, DeleteBannerAction $action): JsonResponse {

        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'delete', $banner);

        $action->handle($banner);

        return Response::api(
            __('Banner deleted successfully'),
            null,
            200
        );
    }

    public function getActiveBannersByPosition(string $position, BannersDispatcher $dispatcher): JsonResponse {

        $banners = $dispatcher->getActiveBannersByPosition($position);

        return Response::api(
            __('Active banners fetched successfully'),
            PublicViewBannerResource::collection($banners)
        );
    }

    public function getPlacements(): JsonResponse {

        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'viewAny', Banner::class);

        $placements = BannerPlacement::toArrayWithLabels();

        return Response::api(
            __('Banner placements fetched successfully'),
            $placements
        );
    }

    public function getContentTypes(Request $request): JsonResponse {

        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'viewAny', Banner::class);

        $action = $request->query('action', 'create');

        $contentTypes = ContentTypeFactory::getRegisteredTypes();

        $formattedTypes = array_map(
            fn($type, $class) => [
                'value' => $type,
                'class' => $class,
                'schema' => ContentTypeFactory::getSchemaForType($type, $action),
            ],
            array_keys($contentTypes),
            array_values($contentTypes)
        );

        return Response::api(
            __('Content types fetched successfully'),
            $formattedTypes
        );
    }

    public function getTargets(): JsonResponse {

        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'viewAny', Banner::class);

        $targets = BannerTarget::toArrayWithLabels();

        return Response::api(
            __('Banner targets fetched successfully'),
            $targets
        );
    }

    public function groupedByPlacements(Request $request, BannersDispatcher $dispatcher): JsonResponse {

        $user = AuthHelper::getUser();

        $this->authorizeForUser($user, 'viewAny', Banner::class);

        $grouped = $dispatcher->getBannersGroupedByPlacement($request->all());

        return Response::api(
            __('Banners grouped by placements fetched successfully'),
            $grouped
        );
    }

}
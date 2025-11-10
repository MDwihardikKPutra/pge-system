<?php

namespace App\Http\Controllers\Concerns;

trait ReturnsJsonResponse
{
    /**
     * Return JSON response if request is AJAX, otherwise return view
     *
     * @param mixed $model
     * @param string $view
     * @param string $dataKey
     * @param array $viewData
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    protected function jsonOrView($model, string $view, string $dataKey, array $viewData = [])
    {
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                $dataKey => $model,
            ]);
        }

        return view($view, array_merge([$dataKey => $model], $viewData));
    }
}




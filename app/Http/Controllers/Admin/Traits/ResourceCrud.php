<?php

namespace App\Http\Controllers\Admin\Traits;

use Illuminate\Http\Request;

trait ResourceCrud
{
    protected $model = \Model::class;
    protected $indexRoute = '';
    protected $trashRoute = '';
    protected $authExcept = ['index', 'show'];
    protected $viewFolder = '';
    protected $viewIndex = '';
    protected $viewCreate = '';
    protected $viewShow = '';
    protected $viewEdit = '';
    protected $validationRules = [];
    protected $validationRuleIdSuffix = [];
    protected $items_per_page = 20;
    protected $listWith = null;

    public function setupCrud()
    {
        if ($this->viewFolder) {
            if (!$this->viewIndex) {
                $this->viewIndex = sprintf('%s.index', $this->viewFolder);
            }

            if (!$this->viewCreate) {
                $this->viewCreate = sprintf('%s.create', $this->viewFolder);
            }

            if (!$this->viewShow) {
                $this->viewShow = sprintf('%s.show', $this->viewFolder);
            }

            if (!$this->viewEdit) {
                $this->viewEdit = sprintf('%s.edit', $this->viewFolder);
            }
        }

        assert($this->model != '');
        assert($this->indexRoute != '');
        assert($this->trashRoute != '');
        assert($this->viewIndex != '');
        assert($this->viewCreate != '');
        assert($this->viewShow != '');
        assert($this->viewEdit != '');
    }

    protected function findOrAbort($id)
    {
        $resource = null;

        $resource = $this->model::withTrashed()->find($id);

        if (!$resource) {
            abort(404, 'Resource not found');
        }

        return $resource;
    }

    public function getBackRoute($item)
    {
        if ($item->trashed()) {
            return $this->trashRoute;
        } else {
            return $this->indexRoute;
        }
    }

    public function index()
    {
        $model = $this->model::orderBy('id', 'desc');

        if ($this->listWith) {
            $model = $model->with($this->listWith);
        }

        $items = $model->paginate($this->items_per_page);

        return view($this->viewIndex)->with([
            'items' => $items,
        ]);
    }

    public function trash()
    {
        $items = $this->model::onlyTrashed()->orderBy('id', 'desc')->paginate($this->items_per_page);

        return view($this->viewIndex)->with([
            'items' => $items,
        ]);
    }

    public function create()
    {
        return view($this->viewCreate);
    }

    public function edit($id)
    {
        $item = $this->findOrAbort($id);

        return view($this->viewEdit)->with(compact('item'));
    }

    public function show($id)
    {
        $item = $this->findOrAbort($id);

        return view($this->viewShow)->with(compact('item'));
    }

    public function store(Request $request)
    {
        $this->validate($request, $this->validationRules);

        $item = $this->model::create($request->all());

        return redirect()->route($this->getBackRoute($item))->with([
            'success' => sprintf('New %s was created!', $item),
        ]);
    }

    public function update(Request $request, $id)
    {
        foreach ($this->validationRuleIdSuffix as $rule) {
            if (key_exists($rule, $this->validationRules)) {
                $this->validationRules[$rule] .= ',' . $id;
            }
        }

        $this->validate($request, $this->validationRules);

        $item = $this->findOrAbort($id);

        $item->update($request->all());

        return redirect()->route($this->getBackRoute($item))->with([
            'success' => sprintf('The %s was updated!', $item),
        ]);
    }

    public function destroy($id)
    {
        $item = $this->findOrAbort($id);

        $item->delete();

        return redirect()->route($this->getBackRoute($item))->with([
            'success' => sprintf('The %s was trashed!', $item),
        ]);
    }

    public function restore($id)
    {
        $item = $this->findOrAbort($id);

        $item->restore();

        return redirect()->route($this->getBackRoute($item))->with([
            'success' => sprintf('The %s was restored!', $item),
        ]);
    }

    public function forceDelete($id)
    {
        $item = $this->findOrAbort($id);

        $item->forceDelete();

        return redirect()->route($this->getBackRoute($item))->with([
            'success' => sprintf('The %s was deleted!', $item),
        ]);
    }
}

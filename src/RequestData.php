<?php

namespace WallaceMaxters\Navalha;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class RequestData
{
    public function __construct(public Request $request)
    {
        $request->validate([
            'component' => 'required|string',
            'method'    => 'required|string',
            'data'      => 'nullable|json',
            'files.*'   => 'nullable|file'
        ]);
    }

    public function payload()
    {
        return collect(json_decode($this->request->get('payload'), true));
    }

    /**
     * @return Collection<int,UploadedFile>
     */
    public function files()
    {
        return collect()->wrap($this->request->file('files'))->filter();
    }


    public function method(): string
    {
        return $this->request->get('method');
    }

    public function component(): string
    {
        return $this->request->get('component');
    }


    public function getAsArguments()
    {
        $arguments = [...$this->payload()];

        $files = $this->files();

        $files->count() && array_unshift($arguments, $files->count() === 1 ? $files[0] : $files);

        return $arguments;
    }
}

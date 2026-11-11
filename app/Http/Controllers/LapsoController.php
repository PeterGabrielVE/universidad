<?php

namespace App\Http\Controllers;

use Alert;
use App\Http\Requests\LapseRequest;
use App\Modules\Academic\Application\UseCase\ManageLapses;
use App\Modules\Academic\Domain\Lapse;
use App\Modules\Academic\Domain\LapseStatus;
use App\Modules\Academic\Infrastructure\LapseDirectory;
use App\Modules\IdentityAccess\Domain\ProgramScope;
use App\Modules\IdentityAccess\Infrastructure\UserDirectory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Lapsos y cohortes. El Administrador gestiona todos; el Coordinador, los de su
 * programa. Las reglas están en ManageLapses / CloseLapse (módulo Academic).
 */
class LapsoController extends Controller
{
    public function __construct(
        private readonly ManageLapses $lapses,
        private readonly UserDirectory $users,
    ) {
    }

    public function index(Request $request, LapseDirectory $directory): View
    {
        $scope = $this->scope($request);

        return view('pages.lapsos.index', [
            'lapsos' => $directory->paginate($scope),
            'programs' => $directory->programsFor($scope),
            'statuses' => LapseStatus::cases(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('lapso.index');
    }

    public function store(LapseRequest $request): RedirectResponse
    {
        $lapse = $this->lapses->create(
            $request->integer('program_id'),
            $request->string('start_lapse')->value(),
            $request->string('end_lapse')->value(),
            $request->status(),
            $this->scope($request),
        );
        Alert::success('Lapso', "«{$lapse->name()}» creado.");

        return redirect()->route('lapso.index');
    }

    public function show(): RedirectResponse
    {
        return redirect()->route('lapso.index');
    }

    /** JSON para el modal de edición. */
    public function edit(Request $request, int $id): JsonResponse
    {
        return response()->json($this->toArray($this->lapses->get($id, $this->scope($request))));
    }

    public function update(LapseRequest $request, int $id): JsonResponse|RedirectResponse
    {
        $lapse = $this->lapses->update(
            $id,
            $request->string('start_lapse')->value(),
            $request->string('end_lapse')->value(),
            $request->status(),
            $this->scope($request),
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'data' => $this->toArray($lapse)]);
        }

        Alert::success('Lapso', "«{$lapse->name()}» actualizado.");

        return redirect()->route('lapso.index');
    }

    public function destroy(Request $request, int $id): RedirectResponse
    {
        $this->lapses->delete($id, $this->scope($request));
        Alert::success('Lapso', 'Eliminado.');

        return redirect()->route('lapso.index');
    }

    private function scope(Request $request): ProgramScope
    {
        return $this->users->programScopeOf($request->user());
    }

    /** @return array{id: int, program_id: ?int, name: string, start_lapse: string, end_lapse: string, status: string} */
    private function toArray(Lapse $lapse): array
    {
        return [
            'id' => $lapse->id(),
            'program_id' => $lapse->programId(),
            'name' => $lapse->name(),
            'start_lapse' => $lapse->period()->start->format('Y-m-d'),
            'end_lapse' => $lapse->period()->end->format('Y-m-d'),
            'status' => $lapse->status()->value,
        ];
    }
}

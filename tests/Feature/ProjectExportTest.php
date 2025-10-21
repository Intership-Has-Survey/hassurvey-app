<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction;

class ProjectExportTest extends TestCase
{
    public function test_list_projects_page_has_export_action_configured()
    {
        // Create instance of ListProjects
        $listProjects = new ListProjects();
        
        // Use reflection to call protected method
        $reflection = new \ReflectionClass($listProjects);
        $method = $reflection->getMethod('getHeaderActions');
        $method->setAccessible(true);
        
        $actions = $method->invoke($listProjects);
        
        // Check if export action exists
        $exportAction = collect($actions)->first(fn($action) => $action instanceof ExportAction);
        
        $this->assertNotNull($exportAction, 'Export action should exist in header actions');
        $this->assertInstanceOf(ExportAction::class, $exportAction, 'Export action should be instance of ExportAction');
    }

    public function test_export_action_has_multiple_export_types()
    {
        $listProjects = new ListProjects();
        
        $reflection = new \ReflectionClass($listProjects);
        $method = $reflection->getMethod('getHeaderActions');
        $method->setAccessible(true);
        
        $actions = $method->invoke($listProjects);
        $exportAction = collect($actions)->first(fn($action) => $action instanceof ExportAction);
        
        $this->assertNotNull($exportAction, 'Export action should exist');
        
        // Get exports property
        $exportsProperty = new \ReflectionProperty($exportAction, 'exports');
        $exportsProperty->setAccessible(true);
        $exports = $exportsProperty->getValue($exportAction);
        
        // Should have 3 export options: table, lengkap, ringkas
        $this->assertInstanceOf(\Illuminate\Support\Collection::class, $exports, 'Exports should be a Collection');
        $this->assertCount(3, $exports, 'Should have 3 export options');
    }

    public function test_export_types_have_correct_names()
    {
        $listProjects = new ListProjects();
        
        $reflection = new \ReflectionClass($listProjects);
        $method = $reflection->getMethod('getHeaderActions');
        $method->setAccessible(true);
        
        $actions = $method->invoke($listProjects);
        $exportAction = collect($actions)->first(fn($action) => $action instanceof ExportAction);
        
        $exportsProperty = new \ReflectionProperty($exportAction, 'exports');
        $exportsProperty->setAccessible(true);
        $exports = $exportsProperty->getValue($exportAction);
        
        // Check export types exist by name
        $exportNames = $exports->map(fn($export) => $export->getName())->all();
        
        $this->assertContains('table', $exportNames, 'Should have table export type');
        $this->assertContains('lengkap', $exportNames, 'Should have lengkap export type');
        $this->assertContains('ringkas', $exportNames, 'Should have ringkas export type');
    }

    public function test_export_action_configuration_is_valid()
    {
        $listProjects = new ListProjects();
        
        $reflection = new \ReflectionClass($listProjects);
        $method = $reflection->getMethod('getHeaderActions');
        $method->setAccessible(true);
        
        $actions = $method->invoke($listProjects);
        
        // Count actions - should have CreateAction and ExportAction
        $this->assertGreaterThanOrEqual(2, count($actions), 'Should have at least 2 header actions');
        
        // Find export action
        $exportAction = null;
        foreach ($actions as $action) {
            if ($action instanceof ExportAction) {
                $exportAction = $action;
                break;
            }
        }
        
        $this->assertNotNull($exportAction, 'Export action should be configured');
    }

    public function test_header_actions_include_create_and_export()
    {
        $listProjects = new ListProjects();
        
        $reflection = new \ReflectionClass($listProjects);
        $method = $reflection->getMethod('getHeaderActions');
        $method->setAccessible(true);
        
        $actions = $method->invoke($listProjects);
        
        // Should have both CreateAction and ExportAction
        $hasCreateAction = false;
        $hasExportAction = false;
        
        foreach ($actions as $action) {
            if (get_class($action) === 'Filament\Actions\CreateAction') {
                $hasCreateAction = true;
            }
            if ($action instanceof ExportAction) {
                $hasExportAction = true;
            }
        }
        
        $this->assertTrue($hasExportAction, 'Should have export action');
        $this->assertTrue($hasCreateAction, 'Should have create action');
    }
}


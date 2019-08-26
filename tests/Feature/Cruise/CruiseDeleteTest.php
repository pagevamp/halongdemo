<?php

namespace Tests\Feature\Cruise;

use App\Repos\Models\Cruise;
use App\Repos\Models\CruiseActivity;
use App\Repos\Models\CruiseCategory;
use App\Repos\Models\CruiseFacility;
use App\Repos\Models\CruiseMeta;
use App\Repos\Models\Experience;
use App\Repos\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Media\CreateMediaTrait;
use Tests\TestCase;

class CruiseDeleteTest extends TestCase
{
    use RefreshDatabase;
    use CreateMediaTrait;
    private $storage;

    public function setUp(): void
    {
        parent::setUp();
        $this->storage = Storage::disk(config('storage.driver.media'));
    }

    public function test_cruise_can_be_deleted_by_admin()
    {
        $this->signInAs('admin');

        $cruise = $this->createCruise();

        $this->deleteJson(route('cruises.delete', $cruise->id))->assertStatus(200);

        $this->assertCount(0, CruiseMeta::all());
        $this->assertCount(0, CruiseCategory::all());
        $this->assertCount(0, CruiseActivity::all());
        $this->assertCount(0, CruiseFacility::all());
    }

    /**
     * Create cruise
     * Create media for cruise
     * Delete cruise
     * Expect media to be deleted.
     */
    public function test_if_media_gets_deleted_after_deleting_cruise()
    {
        $this->signInAs('admin');
        $cruise = $this->createCruise();

        $params = factory(Media::class)->states('with_cruise_gallery')->make([
            'subject_id' => $cruise->id,
            'subject_type' => 'cruises',
        ]);

        $fakeFile = UploadedFile::fake()->image('avatar.jpg');
        $expectedFileName = $this->getExpectedFileName($fakeFile);
        $data = $params->toArray();
        $data['files'] = [$fakeFile];
        $expectedRelativePath = config('storage.folder.media').'/'.$expectedFileName;
        $expectedThumbnails = $this->getExpectedThumbnails($fakeFile, new Cruise());
        $this->postJson(route('media.create'), $data)->assertStatus(201);
        $this->storage->assertExists($expectedRelativePath);
        foreach ($expectedThumbnails as $thumbnail) {
            $this->storage->assertExists($thumbnail['relative_path']);
        }
        $this->deleteJson(route('cruises.delete', $cruise->id))->assertStatus(200);
        $this->storage->assertMissing($expectedRelativePath);
        foreach ($expectedThumbnails as $thumbnail) {
            $this->storage->assertMissing($thumbnail['relative_path']);
        }
    }

    public function test_if_experience_media_gets_deleted_after_deleting_cruise()
    {
        $this->signInAs('admin');
        $cruise = $this->createCruise();

        $params = factory(Media::class)->states('with_cruise_gallery')->make([
            'subject_id' => 1,
            'subject_type' => 'experiences',
        ]);

        $fakeFile = UploadedFile::fake()->image('experience.jpg');
        $expectedFileName = $this->getExpectedFileName($fakeFile);
        $data = $params->toArray();
        $data['files'] = [$fakeFile];
        $expectedRelativePath = config('storage.folder.media').'/'.$expectedFileName;
        $expectedThumbnails = $this->getExpectedThumbnails($fakeFile, new Experience());
        $this->postJson(route('media.create'), $data)->assertStatus(201);
        $this->storage->assertExists($expectedRelativePath);
        foreach ($expectedThumbnails as $thumbnail) {
            $this->storage->assertExists($thumbnail['relative_path']);
        }
        $this->deleteJson(route('cruises.delete', $cruise->id))->assertStatus(200);
        $this->storage->assertMissing($expectedRelativePath);
        foreach ($expectedThumbnails as $thumbnail) {
            $this->storage->assertMissing($thumbnail['relative_path']);
        }
    }

    public function test_cruise_cannot_be_deleted_by_agent()
    {
        $this->signInAs('agent');

        $cruise = factory(Cruise::class)->create();

        $this->deleteJson(route('cruises.delete', $cruise->id))->assertStatus(403);
    }

    public function test_cruise_cannot_be_deleted_by_client()
    {
        $this->signInAs('client');

        $cruise = factory(Cruise::class)->create();

        $this->deleteJson(route('cruises.delete', $cruise->id))->assertStatus(403);
    }
}

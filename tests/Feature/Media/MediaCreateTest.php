<?php

namespace Tests\Feature\Media;

use App\Repos\Models\Cruise;
use App\Repos\Models\Media;
use App\Repos\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MediaCreateTest extends TestCase
{
    use RefreshDatabase;
    use CreateMediaTrait;
    private $storage;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->storage = Storage::disk('public');
    }

    public function test_admin_can_upload_media()
    {
        $this->signInAs('admin');
        $params = factory(Media::class)->states('with_cruise_gallery', 'with_alt_and_featured_index')->make();

        $fakeFile = UploadedFile::fake()->image('avatar.jpg');
        $expectedFileName = $this->getExpectedFileName($fakeFile);
        $data = $params->toArray();
        $data['files'] = [$fakeFile];
        $expectedRelativePath = config('storage.folder.media').'/'.$expectedFileName;
        $expectedThumbnails = $this->getExpectedThumbnails($fakeFile, new Cruise());
        $this->postJson(route('media.create'), $data)->assertStatus(201);

        $path = $this->storage->url($expectedRelativePath);

        $this->assertDatabaseHas('media', [
            'mime_type' => $fakeFile->getMimeType(),
            'extension' => $fakeFile->getClientOriginalExtension(),
            'size' => $fakeFile->getSize(),
            'subject_id' => $params['subject_id'],
            'subject_type' => $params['subject_type'],
            'category' => $params['category'],
            'name' => $expectedFileName,
            'relative_path' => $expectedRelativePath,
            'path' => $path,
            'thumbnails' => json_encode($expectedThumbnails),
        ]);

        $this->storage->assertExists($expectedRelativePath);

        foreach ($expectedThumbnails as $thumbnail) {
            $this->storage->assertExists($thumbnail['relative_path']);
        }
        $this->assertCount(1, Media::all());
    }

    public function createMediaForUser($userId)
    {
        $params = factory(Media::class)->states('with_user_avatar')->make(['subject_id' => $userId]);
        $fakeFile = UploadedFile::fake()->image('avatar.jpg');
        $expectedFileName = $this->getExpectedFileName($fakeFile);
        $data = $params->toArray();
        $data['files'] = [$fakeFile];
        $this->postJson(route('media.create'), $data)->assertStatus(201);
    }

    public function test_if_single_category_images_are_not_duplicated()
    {
        $this->signInAs('admin');
        $user = factory(User::class)->create();
        $this->createMediaForUser($user->id);
        $this->createMediaForUser($user->id);
        $this->assertCount(1, Media::all());
        $this->assertCount(1, $this->storage->files(config('storage.folder.media')));
    }

    public function test_client_cannot_upload_media()
    {
        $this->signInAs('client');
        $this->postJson(route('media.create'), [])->assertStatus(403);
    }

    public function test_agent_cannot_upload_media()
    {
        $this->signInAs('agent');
        $this->postJson(route('media.create'), [])->assertStatus(403);
    }

    public function test_guest_cannot_upload_media()
    {
        $this->postJson(route('media.create'), [])->assertStatus(401);
    }
}

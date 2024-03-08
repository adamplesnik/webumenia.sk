<?php

namespace Tests\Models;

use App\Authority;
use App\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\WithoutSearchIndexing;

class ItemTest extends TestCase
{
    use RefreshDatabase;
    use WithoutSearchIndexing;

    public function testFreeFromDateLatest()
    {
        $item = Item::factory()->make([
            'gallery' => 'Slovenská národná galéria, SNG',
            'date_latest' => 2000,
        ]);

        $this->assertEquals((new \DateTime('2131-01-01'))->getTimestamp(), $item->freeFrom());
    }

    public function testFreeFromAuthorityDeathYear()
    {
        $item = $this->createFreeItem();
        $item->date_latest = date('Y');
        $authority = Authority::factory()->make(['death_year' => 2000]);
        $item->authorities->add($authority);

        $this->assertEquals((new \DateTime('2071-01-01'))->getTimestamp(), $item->freeFrom());
    }

    public function testFreeFromAuthorityDeathYearNull()
    {
        $item = $this->createFreeItem();
        $item->date_latest = date('Y');
        $authority = Authority::factory()->make(['death_year' => null]);
        $item->authorities->add($authority);

        $this->assertTrue($item->isFree());
    }

    public function testIsFree()
    {
        $item = $this->createFreeItem();
        $this->assertTrue($item->isFree());
    }

    public function testIsFreeGallery()
    {
        $item = $this->createFreeItem();
        $item->gallery = '';
        $this->assertFalse($item->isFree());
    }

    public function testIsFreeAuthorDeathYearNow()
    {
        $item = $this->createFreeItem();
        $item->date_latest = date('Y');
        $authority = Authority::factory()->make(['death_year' => date('Y')]);
        $item->authorities->add($authority);
        $this->assertFalse($item->isFree());
    }

    public function testIsFreeDateLatestNow()
    {
        $item = $this->createFreeItem();
        $item->date_latest = date('Y');
        $this->assertTrue($item->isFree());
    }

    public function testGetIndexedDataFallbackLocale()
    {
        /** @var Item $item */
        $item = Item::factory()->make([
            'title' => 'Názov',
            'title:en' => 'Title',
            'description' => 'Popis',
        ]);

        $data = $item->getIndexedData('en');
        $this->assertEquals($data['title'], 'Title');
        $this->assertEquals($data['description'], 'Popis');
    }

    public function testMakeArrayEmpty()
    {
        /** @var Item $item */
        $item = Item::factory()->make();
        $array = $item->makeArray('');
        $this->assertEquals([], $array);
    }

    public function testMakeArrayTrimmed()
    {
        /** @var Item $item */
        $item = Item::factory()->make();
        $array = $item->makeArray(' first ; second ');
        $this->assertEquals(['first', 'second'], $array);
    }

    public function testWorkTypes()
    {
        /** @var Item $item */
        $item = Item::factory()->make([
            'work_type' => 'kresba, prípravná, návrh; iné médiá, album',
        ]);
        $workTypes = $item->work_types;
        $this->assertEquals(
            [
                [
                    [
                        'name' => 'kresba',
                        'path' => 'kresba',
                    ],
                    [
                        'name' => 'prípravná',
                        'path' => 'kresba/prípravná',
                    ],
                    [
                        'name' => 'návrh',
                        'path' => 'kresba/prípravná/návrh',
                    ],
                ],
                [
                    [
                        'name' => 'iné médiá',
                        'path' => 'iné médiá',
                    ],
                    [
                        'name' => 'album',
                        'path' => 'iné médiá/album',
                    ],
                ],
            ],
            $workTypes
        );
    }

    public function testMergedAuthorityNamesAndAuthors()
    {
        $authority = Authority::factory()->create([
            'name' => 'Boudník, Vladimír',
        ]);

        $item = Item::factory()->make([
            'author' => 'Philips Wouwerman; Vladimír Boudník',
        ]);
        $item->authorities()->attach($authority);

        $data = $item->getIndexedData('sk');
        $this->assertCount(2, $data['author']);
        $this->assertEquals('Boudník, Vladimír', $data['author'][0]);
        $this->assertEquals('Philips Wouwerman', $data['author'][1]);
    }

    public function testAuthorsWithAuthoritiesAttribute()
    {
        $item = Item::factory()->make([
            'author' => 'Philips Wouwerman; Vladimír Boudník; Mikuláš Galanda',
        ]);
        $authority = Authority::factory()->create([
            'name' => 'Boudník, Vladimír',
        ]);
        $item->authorities()->attach($authority);

        $data = $item->authors_with_authorities;
        $this->assertCount(3, $data);

        $this->assertEquals('Boudník, Vladimír', $data[0]->name);
        $this->assertInstanceOf(Authority::class, $data[0]->authority);
        $this->assertEquals($authority->id, $data[0]->authority->id);

        $this->assertEquals('Philips Wouwerman', $data[1]->name);
        $this->assertEquals(null, $data[1]->authority);

        $this->markTestSkipped('should list in the order of the author field');
    }

    protected function createFreeItem()
    {
        return Item::factory()->make([
            'gallery' => 'Slovenská národná galéria, SNG',
            'author' => 'neznámy',
            'date_latest' => 1, // CE
        ]);
    }

    public function testSyncMatchedAuthoritiesUpdatesExisting()
    {
        $item = Item::factory()->create();
        [$authority1, $authority2] = Authority::factory()
            ->count(2)
            ->create();
        $item->authorities()->sync([
            $authority1->id => ['role' => 'author', 'automatically_matched' => true],
            $authority2->id => ['role' => 'author', 'automatically_matched' => false],
        ]);

        $item->syncMatchedAuthorities([
            $authority1->id => ['role' => 'new-role-1'],
            $authority2->id => ['role' => 'new-role-2'],
        ]);

        $this->assertDatabaseHas('authority_item', [
            'authority_id' => $authority1->id,
            'item_id' => $item->id,
            'role' => 'new-role-1',
            'automatically_matched' => true,
        ]);
        $this->assertDatabaseHas('authority_item', [
            'authority_id' => $authority2->id,
            'item_id' => $item->id,
            'role' => 'new-role-2',
            'automatically_matched' => false,
        ]);
    }

    public function testSyncMatchedAuthoritiesDeletesOnlyAutomaticallyMatched()
    {
        $item = Item::factory()->create();
        [$authority1, $authority2] = Authority::factory()
            ->count(2)
            ->create();
        $item->authorities()->sync([
            $authority1->id => ['automatically_matched' => true, 'role' => 'author'],
            $authority2->id => ['automatically_matched' => false, 'role' => 'author'],
        ]);

        $item->syncMatchedAuthorities([]);

        $this->assertDatabaseMissing('authority_item', [
            'authority_id' => $authority1->id,
            'item_id' => $item->id,
        ]);
        $this->assertDatabaseHas('authority_item', [
            'authority_id' => $authority2->id,
            'item_id' => $item->id,
            'role' => 'author',
            'automatically_matched' => false,
        ]);
    }

    public function testSyncMatchedAuthoritiesAddsNew()
    {
        $item = Item::factory()->create();
        $authority = Authority::factory()->create();

        $item->syncMatchedAuthorities([
            $authority->id => ['role' => 'author'],
        ]);

        $this->assertDatabaseHas('authority_item', [
            'authority_id' => $authority->id,
            'item_id' => $item->id,
            'role' => 'author',
            'automatically_matched' => true,
        ]);
    }
}

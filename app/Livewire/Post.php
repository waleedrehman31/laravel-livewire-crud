<?php

namespace App\Livewire;

use Livewire\Component;

use App\Models\Post as Posts;
use mysql_xdevapi\Exception;

class Post extends Component
{
    public $posts, $title, $description, $postId, $updatePost = false, $addPost = false;

    protected $listeners = [
        'deletePostListner' => 'deletePost'
    ];

    protected $rules = [
        'title' => 'required',
        'description' => 'required'
    ];

    public function resetFields()
    {
        $this->title = '';
        $this->description = '';
    }

    public function render()
    {
        $this->posts = Post::select('id', 'title', 'description')->get();
        return view('livewire.post');
    }

    public function addPost()
    {
        $this->resetFields();
        $this->addPost = true;
        $this->updatePost = false;
    }

    public function storePost()
    {
        $this->validate();
        try {
            Posts::create([
                'title' => $this->title,
                'description' => $this->description
            ]);
            session()->flash('success', 'Post created Successfully');
            $this->resetFields();
            $this->addPost = false;
        } catch (\Exception $ex) {
            session()->flash('error', 'Something goes wrong!');
        }
    }

    public function editPost($id)
    {
        try {
            $post = Posts::findOrFail($id);
            if (!$post) {
                session()->flash('error', 'Post not found');
            } else {
                $this->title = $post->title;
                $this->description = $post->description;
                $this->postId = $post->id;
                $this->updatePost = true;
                $this->addPost = false;
            }
        } catch (\Exception $ex) {
            session()->flash('error', 'Something goes wrong!');
        }
    }

    public function updatePost()
    {
        $this->validate();
        try {
            Posts::whereId($this->postId)->update([
                'title' => $this->title,
                'description' => $this->description
            ]);
            session()->flash('success', 'Post Updated Successfully');
            $this->resetFields();
            $this->updatePost = false;
        } catch (\Exception $ex) {
            session()->flash('error', 'Something goes wrong!');
        }
    }

    public function cancelPost()
    {
        $this->addPost = false;
        $this->updatePost = false;
        $this->resetFields();
    }

    public function deletePost($id)
    {
        try {
            Posts::find($id)->delete();
            session()->flash('success', 'Post Deleted Successfully');
        } catch (\Exception $ex)
        {
            session()->flash('error', 'Something goes wrong');
        }
    }

}

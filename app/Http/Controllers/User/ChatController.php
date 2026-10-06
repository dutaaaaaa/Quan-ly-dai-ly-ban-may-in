<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function send(Request $request)
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            return response()->json(['error' => 'Bạn cần đăng nhập để gửi tin nhắn.'], 401);
        }

        $messageText = $request->input('message');
        if (empty(trim($messageText))) {
            return response()->json(['error' => 'Nội dung tin nhắn không được để trống'], 400);
        }

        $receiverId = 1; // Mặc định gửi cho Admin (ID 1)

        try {
            $message = Message::create([
                'sender_id' => Auth::id(), // Đảm bảo có ID vì đã Auth::check()
                'receiver_id' => $receiverId,
                'content' => $messageText,
                'is_read' => false,
            ]);
            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }

    public function getMessages()
    {
        // Kiểm tra xem người dùng đã đăng nhập chưa
        if (!Auth::check()) {
            // Trả về mảng rỗng thay vì lỗi 500 để Javascript xử lý êm đẹp
            return response()->json([]);
        }

        $userId = Auth::id();
        $adminId = 1;

        try {
            $messages = Message::with(['sender', 'receiver'])
                ->where(function ($q) use ($userId, $adminId) {
                    $q->where('sender_id', $userId)->where('receiver_id', $adminId);
                })
                ->orWhere(function ($q) use ($userId, $adminId) {
                    $q->where('sender_id', $adminId)->where('receiver_id', $userId);
                })
                ->orderBy('created_at', 'asc')
                ->get();
                
            return response()->json($messages);
        } catch (\Exception $e) {
             return response()->json(['error' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }
}
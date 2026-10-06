<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    // 1. Lấy danh sách khách hàng đã chat với Admin
    public function getUsers()
    {
        $adminId = 1; // ID của Admin luôn là 1 theo luồng chúng ta đã fix

        // Tìm tất cả ID khách hàng đã nhắn tin với Admin (cả gửi và nhận)
        $userIds = Message::where('receiver_id', $adminId)
            ->pluck('sender_id')
            ->merge(Message::where('sender_id', $adminId)->pluck('receiver_id'))
            ->unique()
            ->filter(fn($id) => $id != $adminId); // Loại trừ chính ID của Admin

        // Lấy thông tin User từ các ID đó
        $users = User::whereIn('id', $userIds)->get();

        return response()->json($users);
    }

    // 2. Tải lịch sử chat với 1 khách hàng cụ thể
    public function getMessages($userId)
    {
        $adminId = 1;

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
    }

    // 3. Admin gửi tin nhắn trả lời
    public function send(Request $request, $userId)
    {
        $messageText = $request->input('message');
        if (empty(trim($messageText))) {
            return response()->json(['error' => 'Nội dung không được để trống'], 400);
        }

        try {
            $message = Message::create([
                'sender_id' => 1, // Người gửi là Admin
                'receiver_id' => $userId, // Người nhận là khách hàng đang chọn
                'content' => $messageText,
                'is_read' => false,
            ]);
            return response()->json($message);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Lỗi hệ thống: ' . $e->getMessage()], 500);
        }
    }
}
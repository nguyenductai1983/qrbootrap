<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted'             => ':attribute phải được chấp nhận.',
    'accepted_if'          => ':attribute phải được chấp nhận khi :other là :value.',
    'active_url'           => ':attribute không phải là một URL hợp lệ.',
    'after'                => ':attribute phải là ngày sau :date.',
    'after_or_equal'       => ':attribute phải là ngày sau hoặc bằng :date.',
    'alpha'                => ':attribute chỉ được chứa chữ cái.',
    'alpha_dash'           => ':attribute chỉ được chứa chữ cái, số, dấu gạch ngang và gạch dưới.',
    'alpha_num'            => ':attribute chỉ được chứa chữ cái và số.',
    'array'                => ':attribute phải là một mảng.',
    'ascii'                => ':attribute chỉ được chứa ký tự ASCII một byte và ký hiệu.',
    'before'               => ':attribute phải là ngày trước :date.',
    'before_or_equal'      => ':attribute phải là ngày trước hoặc bằng :date.',
    'between'              => [
        'array'   => ':attribute phải có từ :min đến :max phần tử.',
        'file'    => ':attribute phải có kích thước từ :min đến :max kilobytes.',
        'numeric' => ':attribute phải nằm trong khoảng :min đến :max.',
        'string'  => ':attribute phải có từ :min đến :max ký tự.',
    ],
    'boolean'              => ':attribute phải là true hoặc false.',
    'can'                  => ':attribute chứa giá trị không được phép.',
    'confirmed'            => ':attribute xác nhận không khớp.',
    'current_password'     => 'Mật khẩu hiện tại không chính xác.',
    'date'                 => ':attribute không phải là ngày hợp lệ.',
    'date_equals'          => ':attribute phải là ngày bằng :date.',
    'date_format'          => ':attribute không khớp với định dạng :format.',
    'decimal'              => ':attribute phải có :decimal chữ số thập phân.',
    'declined'             => ':attribute phải bị từ chối.',
    'declined_if'          => ':attribute phải bị từ chối khi :other là :value.',
    'different'            => ':attribute và :other phải khác nhau.',
    'digits'               => ':attribute phải có :digits chữ số.',
    'digits_between'       => ':attribute phải có từ :min đến :max chữ số.',
    'dimensions'           => ':attribute có kích thước ảnh không hợp lệ.',
    'distinct'             => ':attribute có giá trị trùng lặp.',
    'doesnt_end_with'      => ':attribute không được kết thúc bằng một trong các giá trị sau: :values.',
    'doesnt_start_with'    => ':attribute không được bắt đầu bằng một trong các giá trị sau: :values.',
    'email'                => ':attribute phải là địa chỉ email hợp lệ.',
    'ends_with'            => ':attribute phải kết thúc bằng một trong các giá trị sau: :values.',
    'enum'                 => ':attribute được chọn không hợp lệ.',
    'exists'               => ':attribute được chọn không tồn tại trong hệ thống.',
    'file'                 => ':attribute phải là một tệp.',
    'filled'               => ':attribute không được để trống.',
    'gt'                   => [
        'array'   => ':attribute phải có nhiều hơn :value phần tử.',
        'file'    => ':attribute phải lớn hơn :value kilobytes.',
        'numeric' => ':attribute phải lớn hơn :value.',
        'string'  => ':attribute phải có nhiều hơn :value ký tự.',
    ],
    'gte'                  => [
        'array'   => ':attribute phải có :value phần tử trở lên.',
        'file'    => ':attribute phải lớn hơn hoặc bằng :value kilobytes.',
        'numeric' => ':attribute phải lớn hơn hoặc bằng :value.',
        'string'  => ':attribute phải có :value ký tự trở lên.',
    ],
    'image'                => ':attribute phải là hình ảnh.',
    'in'                   => ':attribute được chọn không hợp lệ.',
    'in_array'             => ':attribute không tồn tại trong :other.',
    'integer'              => ':attribute phải là số nguyên.',
    'ip'                   => ':attribute phải là địa chỉ IP hợp lệ.',
    'ipv4'                 => ':attribute phải là địa chỉ IPv4 hợp lệ.',
    'ipv6'                 => ':attribute phải là địa chỉ IPv6 hợp lệ.',
    'json'                 => ':attribute phải là chuỗi JSON hợp lệ.',
    'lowercase'            => ':attribute phải viết thường.',
    'lt'                   => [
        'array'   => ':attribute phải có ít hơn :value phần tử.',
        'file'    => ':attribute phải nhỏ hơn :value kilobytes.',
        'numeric' => ':attribute phải nhỏ hơn :value.',
        'string'  => ':attribute phải có ít hơn :value ký tự.',
    ],
    'lte'                  => [
        'array'   => ':attribute không được có nhiều hơn :value phần tử.',
        'file'    => ':attribute phải nhỏ hơn hoặc bằng :value kilobytes.',
        'numeric' => ':attribute phải nhỏ hơn hoặc bằng :value.',
        'string'  => ':attribute phải có :value ký tự trở xuống.',
    ],
    'mac_address'          => ':attribute phải là địa chỉ MAC hợp lệ.',
    'max'                  => [
        'array'   => ':attribute không được có nhiều hơn :max phần tử.',
        'file'    => ':attribute không được vượt quá :max kilobytes.',
        'numeric' => ':attribute không được lớn hơn :max.',
        'string'  => ':attribute không được vượt quá :max ký tự.',
    ],
    'max_digits'           => ':attribute không được có nhiều hơn :max chữ số.',
    'mimes'                => ':attribute phải là tệp thuộc loại: :values.',
    'mimetypes'            => ':attribute phải là tệp thuộc loại: :values.',
    'min'                  => [
        'array'   => ':attribute phải có ít nhất :min phần tử.',
        'file'    => ':attribute phải có kích thước tối thiểu :min kilobytes.',
        'numeric' => ':attribute phải lớn hơn hoặc bằng :min.',
        'string'  => ':attribute phải có ít nhất :min ký tự.',
    ],
    'min_digits'           => ':attribute phải có ít nhất :min chữ số.',
    'missing'              => ':attribute phải thiếu.',
    'missing_if'           => ':attribute phải thiếu khi :other là :value.',
    'missing_unless'       => ':attribute phải thiếu trừ khi :other là :value.',
    'missing_with'         => ':attribute phải thiếu khi :values có mặt.',
    'missing_with_all'     => ':attribute phải thiếu khi :values đều có mặt.',
    'multiple_of'          => ':attribute phải là bội số của :value.',
    'not_in'               => ':attribute được chọn không hợp lệ.',
    'not_regex'            => ':attribute có định dạng không hợp lệ.',
    'numeric'              => ':attribute phải là một số.',
    'password'             => [
        'letters'       => ':attribute phải chứa ít nhất một chữ cái.',
        'mixed'         => ':attribute phải chứa ít nhất một chữ hoa và một chữ thường.',
        'numbers'       => ':attribute phải chứa ít nhất một số.',
        'symbols'       => ':attribute phải chứa ít nhất một ký hiệu.',
        'uncompromised' => ':attribute đã xuất hiện trong rò rỉ dữ liệu. Vui lòng chọn :attribute khác.',
    ],
    'present'              => ':attribute phải có mặt.',
    'prohibited'           => ':attribute bị cấm.',
    'prohibited_if'        => ':attribute bị cấm khi :other là :value.',
    'prohibited_unless'    => ':attribute bị cấm trừ khi :other nằm trong :values.',
    'prohibits'            => ':attribute cấm :other có mặt.',
    'regex'                => ':attribute có định dạng không hợp lệ.',
    'required'             => ':attribute không được để trống.',
    'required_array_keys'  => ':attribute phải chứa các mục: :values.',
    'required_if'          => ':attribute không được để trống khi :other là :value.',
    'required_if_accepted' => ':attribute không được để trống khi :other được chấp nhận.',
    'required_unless'      => ':attribute không được để trống trừ khi :other nằm trong :values.',
    'required_with'        => ':attribute không được để trống khi :values có mặt.',
    'required_with_all'    => ':attribute không được để trống khi :values đều có mặt.',
    'required_without'     => ':attribute không được để trống khi :values không có mặt.',
    'required_without_all' => ':attribute không được để trống khi không có :values nào có mặt.',
    'same'                 => ':attribute và :other phải khớp nhau.',
    'size'                 => [
        'array'   => ':attribute phải chứa :size phần tử.',
        'file'    => ':attribute phải có kích thước :size kilobytes.',
        'numeric' => ':attribute phải bằng :size.',
        'string'  => ':attribute phải có :size ký tự.',
    ],
    'starts_with'          => ':attribute phải bắt đầu bằng một trong các giá trị sau: :values.',
    'string'               => ':attribute phải là một chuỗi.',
    'timezone'             => ':attribute phải là múi giờ hợp lệ.',
    'unique'               => ':attribute đã được sử dụng.',
    'uploaded'             => ':attribute tải lên thất bại.',
    'uppercase'            => ':attribute phải viết hoa.',
    'url'                  => ':attribute phải là URL hợp lệ.',
    'ulid'                 => ':attribute phải là ULID hợp lệ.',
    'uuid'                 => ':attribute phải là UUID hợp lệ.',

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    */
    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    */
    'attributes' => [
        'name'              => 'Tên',
        'username'          => 'Tên đăng nhập',
        'email'             => 'Email',
        'password'          => 'Mật khẩu',
        'department_id'     => 'Bộ phận',
        'shift_id'          => 'Ca làm việc',
        'selectedRoles'     => 'Vai trò',
        'selectedRoles.*'   => 'Vai trò',
        'selectedPermissions'   => 'Quyền',
        'selectedPermissions.*' => 'Quyền',
        'is_admin'          => 'Quyền quản trị',
        'force_password_change' => 'Bắt buộc đổi mật khẩu',
    ],

];
